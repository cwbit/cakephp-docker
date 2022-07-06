<?php
declare(strict_types=1);

namespace Duplicatable\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Association;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Behavior;

/**
 * Behavior for duplicating entities (including related entities)
 *
 * Configurable options:
 * - finder: Finder to use. Defaults to 'all'.
 * - contain: related entities to duplicate
 * - includeTranslations: set true to duplicate translations.
 *   This option is deprecated, instead set "finder" to "translations".
 * - remove: fields to remove
 * - set: fields and their default value
 * - prepend: fields and text to prepend
 * - append: fields and text to append
 * - preserveJoinData: if _joinData on BelongsToMany relations should be preserved
 */
class DuplicatableBehavior extends Behavior
{
    /**
     * Default options
     *
     * @var array
     */
    protected $_defaultConfig = [
        'finder' => 'all',
        'contain' => [],
        'includeTranslations' => false,
        'remove' => [],
        'set' => [],
        'prepend' => [],
        'append' => [],
        'saveOptions' => [],
        'preserveJoinData' => false,
    ];

    /**
     * Duplicate record.
     *
     * @param int|string $id Id of entity to duplicate.
     * @return \Cake\Datasource\EntityInterface|false New entity or false on failure
     */
    public function duplicate($id)
    {
        return $this->_table->save(
            $this->duplicateEntity($id),
            $this->getConfig('saveOptions') + ['associated' => $this->getConfig('contain')]
        );
    }

    /**
     * Creates duplicate Entity for given record id without saving it.
     *
     * @param int|string $id Id of entity to duplicate.
     * @return \Cake\Datasource\EntityInterface
     */
    public function duplicateEntity($id): EntityInterface
    {
        $query = $this->_table;
        foreach ($this->_getFinder() as $finder) {
            $query = $query->find($finder);
        }

        $contain = $this->_getContain();

        if (!empty($contain)) {
            $query = $query->contain($contain);
        }

        /** @var \Cake\Datasource\EntityInterface $entity */
        $entity = $query
            ->where([$this->_table->getAlias() . '.' . $this->_table->getPrimaryKey() => $id])
            ->firstOrFail();

        // process entity
        foreach ($this->getConfig('contain') as $contain) {
            $parts = explode('.', $contain);
            $this->_drillDownAssoc($entity, $this->_table, $parts);
        }

        $this->_modifyEntity($entity, $this->_table);

        foreach ($this->getConfig('remove') as $field) {
            $parts = explode('.', $field);
            $this->_drillDownEntity('remove', $entity, $parts);
        }

        foreach (['set', 'prepend', 'append'] as $action) {
            foreach ($this->getConfig($action) as $field => $value) {
                $parts = explode('.', $field);
                $this->_drillDownEntity($action, $entity, $parts, $value);
            }
        }

        return $entity;
    }

    /**
     * Return finder to use for fetching entities.
     *
     * @param string|null $assocPath Dot separated association path. E.g. Invoices.InvoiceItems
     * @return array
     */
    protected function _getFinder(?string $assocPath = null): array
    {
        $finders = $this->getConfig('finder');

        if (!is_array($finders)) {
            $finders = [$finders];
        }

        // for backward compatibility
        if ($this->getConfig('includeTranslations')) {
            $finders[] = 'translations';
        }

        if ($finders === ['all']) {
            return $finders;
        }

        $object = $this->_table;
        if ($assocPath) {
            $parts = explode('.', $assocPath);
            foreach ($parts as $prop) {
                $object = $object->{$prop};
            }
        }

        $tmp = [];
        foreach ($finders as $finder) {
            if ($object->hasFinder($finder)) {
                $tmp[] = $finder;
            }
        }

        if (empty($tmp)) {
            $tmp = ['all'];
        }

        $finders = array_unique($tmp);

        return $finders;
    }

    /**
     * Return the contain array modified to use custom finder as required.
     *
     * @return array
     */
    protected function _getContain(): array
    {
        $contain = [];
        foreach ($this->getConfig('contain') as $assocPath) {
            $finders = $this->_getFinder($assocPath);
            if ($finders === ['all']) {
                $contain[] = $assocPath;
            } else {
                $contain[$assocPath] = function ($query) use ($finders) {
                    foreach ($finders as $finder) {
                        $query->find($finder);
                    }

                    return $query;
                };
            }
        }

        return $contain;
    }

    /**
     * Modify entity
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param \Cake\ORM\Table|\Cake\ORM\Association $object Table or association instance.
     * @return void
     */
    protected function _modifyEntity(EntityInterface $entity, $object): void
    {
        // belongs to many is tricky
        if ($object instanceof BelongsToMany && !$this->getConfig('preserveJoinData')) {
            unset($entity->_joinData);
        } elseif (!$object instanceof BelongsToMany) {
            // unset primary key
            unset($entity->{$object->getPrimaryKey()});

            // unset foreign key
            if ($object instanceof Association) {
                unset($entity->{$object->getPrimaryKey()});
            }
        }

        // set translations as new
        if (!empty($entity->_translations)) {
            foreach ($entity->_translations as $translation) {
                $translation->setNew(true);
            }
        }

        // set as new
        $entity->setNew(true);
    }

    /**
     * Drill down the related properties based on containments and modify each entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param \Cake\ORM\Table|\Cake\ORM\Association $object Table or association instance.
     * @param array $parts Related properties chain.
     * @return void
     */
    protected function _drillDownAssoc(EntityInterface $entity, $object, array $parts): void
    {
        $assocName = array_shift($parts);
        $prop = $object->{$assocName}->getProperty();
        $associated = $entity->{$prop};

        if (empty($associated) || $object->{$assocName} instanceof BelongsTo) {
            return;
        }

        if ($associated instanceof EntityInterface) {
            if (!empty($parts)) {
                $this->_drillDownAssoc($associated, $object->{$assocName}, $parts);
            }

            if (!$associated->isNew()) {
                $this->_modifyEntity($associated, $object->{$assocName});
            }

            return;
        }

        foreach ($associated as $e) {
            if (!empty($parts)) {
                $this->_drillDownAssoc($e, $object->{$assocName}, $parts);
            }

            if (!$e->isNew()) {
                $this->_modifyEntity($e, $object->{$assocName});
            }
        }
    }

    /**
     * Drill down the properties and modify the leaf property.
     *
     * @param string $action Action to perform.
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param array $parts Related properties chain.
     * @param mixed $value Value to set or use for modification.
     * @return void
     */
    protected function _drillDownEntity(string $action, EntityInterface $entity, array $parts, $value = null): void
    {
        $prop = array_shift($parts);
        if (empty($parts)) {
            $this->_doAction($action, $entity, $prop, $value);

            return;
        }

        if ($entity->{$prop} instanceof EntityInterface) {
            $this->_drillDownEntity($action, $entity->{$prop}, $parts, $value);

            return;
        }

        if (is_iterable($entity->{$prop})) {
            foreach ($entity->{$prop} as $e) {
                $this->_drillDownEntity($action, $e, $parts, $value);
            }
        }
    }

    /**
     * Perform specified action.
     *
     * @param string $action Action to perform.
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param string $prop Property name.
     * @param mixed $value Value to set or use for modification.
     * @return void
     */
    protected function _doAction(string $action, EntityInterface $entity, $prop, $value = null): void
    {
        switch ($action) {
            case 'remove':
                $entity->unset($prop);

                if (!empty($entity->_translations)) {
                    foreach ($entity->_translations as &$translation) {
                        $translation->unset($prop);
                    }
                }
                break;

            case 'set':
                if (!is_string($value) && is_callable($value)) {
                    $value = $value($entity);
                }
                $entity->set($prop, $value);

                if (!empty($entity->_translations)) {
                    foreach ($entity->_translations as &$translation) {
                        $translation->set($prop, $value);
                    }
                }
                break;

            case 'prepend':
                $entity->set($prop, $value . $entity->get($prop));

                if (!empty($entity->_translations)) {
                    foreach ($entity->_translations as &$translation) {
                        if (!is_null($translation->get($prop))) {
                            $translation->set($prop, $value . $translation->get($prop));
                        }
                    }
                }
                break;

            case 'append':
                $entity->set($prop, $entity->get($prop) . $value);

                if (!empty($entity->_translations)) {
                    foreach ($entity->_translations as &$translation) {
                        if (!is_null($translation->get($prop))) {
                            $translation->set($prop, $translation->get($prop) . $value);
                        }
                    }
                }
                break;
        }
    }
}
