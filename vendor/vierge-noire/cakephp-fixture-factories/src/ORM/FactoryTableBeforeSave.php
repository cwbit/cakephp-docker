<?php
declare(strict_types=1);

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2020 Juan Pablo Ramirez and Nicolas Masson
 * @link          https://webrider.de/
 * @since         1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace CakephpFixtureFactories\ORM;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Table;
use CakephpFixtureFactories\Factory\DataCompiler;

/**
 * Class FactoryTableBeforeSave
 *
 * @internal
 */
final class FactoryTableBeforeSave
{
    /**
     * @var \Cake\ORM\Table
     */
    protected $table;

    /**
     * @var \Cake\Datasource\EntityInterface
     */
    protected $entity;

    /**
     * @param  \Cake\ORM\Table $table Table on which the beforeFind actions are taken.
     * @param  \Cake\Datasource\EntityInterface $entity Entity concerned by the saving.
     */
    public function __construct(Table $table, EntityInterface $entity)
    {
        $this->setTable($table);
        $this->setEntity($entity);
    }

    /**
     * @param  \Cake\ORM\Table $table Table on which the beforeFind actions are taken.
     * @param  \Cake\Datasource\EntityInterface $entity Entity concerned by the saving.
     * @return void
     */
    public static function handle(Table $table, EntityInterface $entity): void
    {
        $handler = new static($table, $entity);

        $handler->handleUniqueFields();
    }

    /**
     * This is triggered only in associated entities.
     * Fetched in the entity the properties marked by the data compiler
     * as unique and non-random. Look for duplicates. If found, no
     * new associated entity is created, but the exisiting gets updated.
     *
     * @return void
     */
    public function handleUniqueFields(): void
    {
        $filter = $this->getEnforcedUniquePropertyValues();

        if (!empty($filter) && $this->getIsAssociated()) {
            $duplicate = $this->findDuplicate($filter);
            if ($duplicate) {
                $this->patchDuplicateOntoEntity($duplicate);
            }
        }

        $this->unsetEntityTemporaryProperties();
    }

    /**
     * Get the entities that the datacompiler marked as dirty
     * while creating then entity
     *
     * @see DataCompiler::compileEntity()
     * @return array
     */
    public function getEntityModifiedUniqueProperties(): array
    {
        return $this->getEntity()->get(DataCompiler::MODIFIED_UNIQUE_PROPERTIES) ?? [];
    }

    /**
     * @return bool
     */
    public function getIsAssociated(): bool
    {
        return $this->getEntity()->get(DataCompiler::IS_ASSOCIATED) ?? false;
    }

    /**
     * @return void
     */
    public function unsetEntityTemporaryProperties()
    {
        $this->getEntity()->unset(DataCompiler::MODIFIED_UNIQUE_PROPERTIES);
        $this->getEntity()->unset(DataCompiler::IS_ASSOCIATED);
    }

    /**
     * @param array $conditions Conditions that a duplicate should meet
     * @return array|null
     */
    public function findDuplicate(array $conditions)
    {
        /** @var array|null $duplicate */
        $duplicate = $this->getTable()
            ->find()
            ->select($this->getPropertiesToPatchFromDuplicate())
            ->where($conditions)
            ->disableHydration()
            ->first();

        return $duplicate;
    }

    /**
     * Knowing which fields were specified by the developer in the the factory,
     * extract their values in the entity in an array to prepare a search
     * for a duplicate.
     *
     * @return array
     */
    public function getEnforcedUniquePropertyValues(): array
    {
        $filter = [];
        foreach ($this->getEntityModifiedUniqueProperties() as $uniqueField) {
            $filter[$uniqueField] = $this->getEntity()->get($uniqueField);
        }

        return $filter;
    }

    /**
     * If the entity about to be saved has a duplicate,
     * the primary keys and the fields modified unique
     * fields shall be overwritten by the already existing entity.
     *
     * @param array $duplicate Values to patch from the existing entity to the one about to be created.
     * @return void
     */
    public function patchDuplicateOntoEntity(array $duplicate): void
    {
        $this->getEntity()->setNew(false);
        $this->getEntity()->clean();
        foreach ($this->getPropertiesToPatchFromDuplicate() as $field) {
            $this->getEntity()->set($field, $duplicate[$field]);
        }
    }

    /**
     * Merge unique fields enforced by the developer and the
     * primary keys. Those will define which fields to search
     * in duplicate.
     *
     * @return array
     */
    public function getPropertiesToPatchFromDuplicate(): array
    {
        return array_unique(
            array_merge(
                (array)$this->getTable()->getPrimaryKey(),
                $this->getEntityModifiedUniqueProperties()
            )
        );
    }

    /**
     * @return \Cake\ORM\Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @param \Cake\ORM\Table $table The class's table.
     * @return void
     */
    public function setTable(Table $table): void
    {
        $this->table = $table;
    }

    /**
     * @return \Cake\Datasource\EntityInterface
     */
    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }

    /**
     * @param \Cake\Datasource\EntityInterface $entity The class's entity.
     * @return void
     */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }
}
