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

namespace CakephpFixtureFactories\Factory;

use Cake\Database\Driver\Postgres;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\HasOne;
use Cake\Utility\Inflector;
use CakephpFixtureFactories\Error\FixtureFactoryException;
use CakephpFixtureFactories\Error\PersistenceException;
use InvalidArgumentException;

/**
 * Class DataCompiler
 *
 * @internal
 */
class DataCompiler
{
    public const MODIFIED_UNIQUE_PROPERTIES = '___data_compiler__modified_unique_properties';
    public const IS_ASSOCIATED = '___data_compiler__is_associated';

    private $dataFromDefaultTemplate = [];
    private $dataFromInstantiation = [];
    private $dataFromPatch = [];
    private $dataFromAssociations = [];
    private $dataFromDefaultAssociations = [];
    private $primaryKeyOffset = [];
    private $enforcedFields = [];

    private static $inPersistMode = false;

    /**
     * @var \CakephpFixtureFactories\Factory\BaseFactory
     */
    private $factory;

    /**
     * DataCompiler constructor.
     *
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Master factory
     */
    public function __construct(BaseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Data passed in the instantiation by array
     *
     * @param array|\Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[] $data Injected data.
     * @return void
     */
    public function collectFromInstantiation($data): void
    {
        $this->dataFromInstantiation = $data;
    }

    /**
     * Data passed in the instantiation by callable
     *
     * @param callable $fn Injected callable
     * @return void
     */
    public function collectArrayFromCallable(callable $fn): void
    {
        // if the callable returns an array, add it the the templateData array, so it will be compiled
        $returnValue = $fn($this->getFactory(), $this->getFactory()->getFaker());
        if (is_array($returnValue)) {
            $this->dataFromInstantiation = $fn;
        }
    }

    /**
     * @param array $data Collected data
     * @return void
     */
    public function collectFromPatch(array $data): void
    {
        $this->dataFromPatch = array_merge($this->dataFromPatch, $data);
    }

    /**
     * @param callable $fn Collected data from default template
     * @return void
     */
    public function collectFromDefaultTemplate(callable $fn): void
    {
        $this->dataFromDefaultTemplate = $fn;
    }

    /**
     * @param string $associationName Association name
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Collected factory
     * @param bool $isToOne is the association a toOne
     * @return void
     */
    public function collectAssociation(string $associationName, BaseFactory $factory, bool $isToOne): void
    {
        if ($isToOne) {
            $associationFieldName = Inflector::underscore(Inflector::singularize($associationName));
            if (
                $this->dataFromInstantiation instanceof EntityInterface &&
                $this->dataFromInstantiation->has($associationFieldName)
            ) {
                $factory->patchData($this->dataFromInstantiation->get($associationFieldName));
            } elseif (
                is_array($this->dataFromInstantiation) &&
                isset($this->dataFromInstantiation[$associationFieldName])
            ) {
                $factory->patchData($this->dataFromInstantiation[$associationFieldName]);
            }
        }
        if (isset($this->dataFromAssociations[$associationName])) {
            $this->dataFromAssociations[$associationName][] = $factory;
        } else {
            $this->dataFromAssociations[$associationName] = [$factory];
        }
    }

    /**
     * Scan for the data stored in the $association path provided and drop it
     *
     * @param string $associationName Association name
     * @return void
     */
    public function dropAssociation(string $associationName): void
    {
        unset($this->dataFromAssociations[$associationName]);
        unset($this->dataFromDefaultAssociations[$associationName]);
    }

    /**
     * Populate the factored entity
     *
     * @return \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]
     */
    public function getCompiledTemplateData()
    {
        $setPrimaryKey = $this->isInPersistMode();

        if (is_array($this->dataFromInstantiation) && isset($this->dataFromInstantiation[0])) {
            $compiledTemplateData = [];
            foreach ($this->dataFromInstantiation as $entity) {
                if ($entity instanceof BaseFactory) {
                    foreach ($entity->getEntities() as $subEntity) {
                        $compiledTemplateData[] = $this->compileEntity($subEntity, $setPrimaryKey);
                        $setPrimaryKey = false;
                    }
                } else {
                    $compiledTemplateData[] = $this->compileEntity($entity, $setPrimaryKey);
                    // Only the first entity gets its primary key set.
                    $setPrimaryKey = false;
                }
            }
        } else {
            $compiledTemplateData = $this->compileEntity($this->dataFromInstantiation, $setPrimaryKey);
        }

        return $compiledTemplateData;
    }

    /**
     * @param array|callable|\Cake\Datasource\EntityInterface $injectedData Data from the injection.
     * @param bool $setPrimaryKey Set the primary key if this entity is alone or the first of an array.
     * @return \Cake\Datasource\EntityInterface
     */
    public function compileEntity($injectedData = [], bool $setPrimaryKey = false): EntityInterface
    {
        if ($injectedData instanceof EntityInterface) {
            $entity = $injectedData;
        } else {
            $entity = $this->getEntityFromDefaultTemplate();
            $this->mergeWithInjectedData($entity, $injectedData);
        }

        $this->mergeWithPatchedData($entity)->mergeWithAssociatedData($entity);

        if ($this->isInPersistMode() && !empty($this->getModifiedUniqueFields())) {
            $entity->set(self::MODIFIED_UNIQUE_PROPERTIES, $this->getModifiedUniqueFields());
        }

        if ($setPrimaryKey) {
            $this->setPrimaryKey($entity);
        }

        return $entity;
    }

    /**
     * Helper method to patch entities with the data compiler data.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to patch.
     * @param array $data Data to patch.
     * @return \Cake\Datasource\EntityInterface
     */
    private function patchEntity(EntityInterface $entity, array $data): EntityInterface
    {
        return empty($data) ? $entity : $this->getFactory()->getTable()->patchEntity(
            $entity,
            $data,
            $this->getFactory()->getMarshallerOptions()
        );
    }

    /**
     * Step 1: Create an entity from the default template.
     *
     * @return \Cake\Datasource\EntityInterface
     */
    private function getEntityFromDefaultTemplate(): EntityInterface
    {
        $data = $this->dataFromDefaultTemplate;
        if (is_callable($data)) {
            $data = $data($this->getFactory()->getFaker());
        }

        return $this->getFactory()->getTable()->newEntity($data, $this->getFactory()->getMarshallerOptions());
    }

    /**
     * Step 2:
     * Merge with the data injected during the instantiation of the Factory
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to manipulate.
     * @param array|callable|\Cake\Datasource\EntityInterface $data Data from the instantiation.
     * @return self
     */
    private function mergeWithInjectedData(EntityInterface $entity, $data): self
    {
        if (is_callable($data)) {
            $data = $data(
                $this->getFactory(),
                $this->getFactory()->getFaker()
            );
        } elseif (is_array($data)) {
            $this->addEnforcedFields($data);
        }

        $this->patchEntity($entity, $data);

        return $this;
    }

    /**
     * Step 3:
     * Merge with the data gathered by patching.
     * At this point, the developer all the data
     * modified by the user is known ("enforced fields").
     * This will be passed as field to the dedicated table's
     * beforeFind in order to handle the uniqueness of its fields.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to manipulate.
     * @return self
     */
    private function mergeWithPatchedData(EntityInterface $entity): self
    {
        $this->patchEntity($entity, $this->dataFromPatch);
        $this->addEnforcedFields($this->dataFromPatch);

        return $this;
    }

    /**
     * Step 4:
     * Merge with the data from the associations
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity produced by the factory.
     * @return self
     */
    private function mergeWithAssociatedData(EntityInterface $entity): self
    {
        // Overwrite the default associations if these are found in the associations
        $associatedData = array_merge($this->dataFromDefaultAssociations, $this->dataFromAssociations);

        foreach ($associatedData as $propertyName => $data) {
            $association = $this->getAssociationByPropertyName($propertyName);
            $propertyName = $this->getMarshallerAssociationName($propertyName);
            if ($association instanceof HasOne || $association instanceof BelongsTo) {
                // toOne associated data must be singular when saved
                $this->mergeWithToOne($entity, $propertyName, $data);
            } else {
                $this->mergeWithToMany($entity, $propertyName, $data);
            }
        }

        return $this;
    }

    /**
     * There might be several data feeding a toOne relation
     * One reason can be the default template value.
     * Here the latest inserted record is taken
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity produced by the factory.
     * @param string $associationName Association
     * @param array $data Data to inject
     * @return void
     */
    private function mergeWithToOne(EntityInterface $entity, string $associationName, array $data): void
    {
        $count = count($data);
        /** @var \CakephpFixtureFactories\Factory\BaseFactory $factory */
        $factory = $data[$count - 1];

        $associatedEntity = $factory->getEntity();
        if ($this->isInPersistMode()) {
            $associatedEntity->set(self::IS_ASSOCIATED, true);
        }

        $entity->set($associationName, $associatedEntity);
    }

    /**
     * @param \Cake\Datasource\EntityInterface $entity Entity produced by the factory.
     * @param string $associationName Association
     * @param array $data Data to inject
     * @return void
     */
    private function mergeWithToMany(EntityInterface $entity, string $associationName, array $data): void
    {
        $associationData = $entity->get($associationName);
        foreach ($data as $factory) {
            if (empty($associationData)) {
                $associationData = $this->getManyEntities($factory);
            } else {
                $associationData = array_merge($associationData, $this->getManyEntities($factory));
            }
        }
        $entity->set($associationName, $associationData);
    }

    /**
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Factory
     * @return \Cake\Datasource\EntityInterface[]
     */
    private function getManyEntities(BaseFactory $factory): array
    {
        $entities = $factory->getEntities();
        if ($this->isInPersistMode()) {
            foreach ($entities as $entity) {
                $entity->set(self::IS_ASSOCIATED, true);
            }
        }

        return $entities;
    }

    /**
     * Used in the Factory make in order to distinguish default associations
     * from conscious associations
     *
     * @return void
     */
    public function collectAssociationsFromDefaultTemplate(): void
    {
        $this->dataFromDefaultAssociations = $this->dataFromAssociations;
        $this->dataFromAssociations = [];
    }

    /**
     * Returns the property name of the association. This can be dot separated for deep associations
     * Throws an exception if the association name does not exist on the rootTable of the factory
     *
     * @param string $associationName Association
     * @return string underscore_version of the input string
     * @throws \InvalidArgumentException
     */
    public function getMarshallerAssociationName(string $associationName): string
    {
        $result = [];
        $cast = explode('.', $associationName);
        $table = $this->getFactory()->getTable();
        foreach ($cast as $ass) {
            $association = $table->getAssociation($ass);
            $result[] = $association->getProperty();
            $table = $association->getTarget();
        }

        return implode('.', $result);
    }

    /**
     * @param string $propertyName Property
     * @return bool|\Cake\ORM\Association
     */
    public function getAssociationByPropertyName(string $propertyName)
    {
        try {
            return $this->getFactory()->getTable()->getAssociation(Inflector::camelize($propertyName));
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param \Cake\Datasource\EntityInterface $entity Entity to manipulate.
     * @return \Cake\Datasource\EntityInterface
     */
    public function setPrimaryKey(EntityInterface $entity): EntityInterface
    {
        // A set of primary keys is produced if in persistence mode, and if a first set was not produced yet
        if (!$this->isInPersistMode() || !is_array($this->primaryKeyOffset)) {
            return $entity;
        }

        foreach ($this->createPrimaryKeyOffset() as $pk => $value) {
            if (!$entity->has($pk)) {
                $entity->set($pk, $value);
            }
        }

        return $entity;
    }

    /**
     * @return array
     */
    public function createPrimaryKeyOffset(): array
    {
        if (!is_array($this->primaryKeyOffset)) {
            throw new PersistenceException('A set of primary keys was already created');
        }
        $res = empty($this->primaryKeyOffset) ? $this->generateArrayOfRandomPrimaryKeys() : $this->primaryKeyOffset;

        $this->updatePostgresSequence($res);

        // Set to null, this factory will never generate a primaryKeyOffset again
        $this->primaryKeyOffset = null;

        return $res;
    }

    /**
     * @return array
     */
    public function generateArrayOfRandomPrimaryKeys(): array
    {
        $primaryKeys = (array)$this->getFactory()->getTable()->getPrimaryKey();
        $res = [];
        foreach ($primaryKeys as $pk) {
            $res[$pk] = $this->generateRandomPrimaryKey(
                $this->getFactory()->getTable()->getSchema()->getColumnType($pk)
            );
        }

        return $res;
    }

    /**
     * Credits to Faker
     * https://github.com/fzaninotto/Faker/blob/master/src/Faker/ORM/CakePHP/ColumnTypeGuesser.php
     *
     * @param string $columnType Column type
     * @return int|string
     */
    public function generateRandomPrimaryKey(string $columnType)
    {
        switch ($columnType) {
            case 'uuid':
            case 'string':
                $res = $this->getFactory()->getFaker()->uuid();
                break;
            case 'biginteger':
                $res = mt_rand(0, intval('9223372036854775807'));
                break;
            case 'integer':
            default:
                $res = mt_rand(0, intval('2147483647'));
                break;
        }

        return $res;
    }

    /**
     * @return \CakephpFixtureFactories\Factory\BaseFactory
     */
    public function getFactory(): BaseFactory
    {
        return $this->factory;
    }

    /**
     * @param int|string|array $primaryKeyOffset Name of the primary key
     * @return void
     */
    public function setPrimaryKeyOffset($primaryKeyOffset): void
    {
        if (is_int($primaryKeyOffset) || is_string($primaryKeyOffset)) {
            $primaryKey = $this->getFactory()->getTable()->getPrimaryKey();
            if (!is_string($primaryKey)) {
                throw new FixtureFactoryException(
                    "The primary key assigned must be a string as $primaryKeyOffset is a string or an integer."
                );
            }
            $this->primaryKeyOffset = [
                $primaryKey => $primaryKeyOffset,
            ];
        } elseif (is_array($primaryKeyOffset)) {
            $this->primaryKeyOffset = $primaryKeyOffset;
        } else {
            throw new FixtureFactoryException(
                "$primaryKeyOffset must be an integer, a string or an array of format ['primaryKey1' => value, ...]"
            );
        }
    }

    /**
     * @param array $primaryKeys Set of primary keys
     * @return void
     */
    private function updatePostgresSequence(array $primaryKeys): void
    {
        $table = $this->getFactory()->getTable();
        if ($table->getConnection()->config()['driver'] === Postgres::class) {
            $tableName = $table->getTable();

            foreach ($primaryKeys as $pk => $offset) {
                $seq = $table->getConnection()->execute("
		            SELECT pg_get_serial_sequence('$tableName','$pk')")->fetchAll()[0][0];
                if ($seq !== null) {
                    $table->getConnection()->execute(
                        "SELECT setval('$seq', $offset);"
                    );
                }
            }
        }
    }

    /**
     * Fetch the fields that were intentionally modified by the developer
     * and that are unique. These should be watched for uniqueness.
     *
     * @return array
     */
    public function getModifiedUniqueFields(): array
    {
        return array_values(
            array_intersect(
                $this->getEnforcedFields(),
                array_merge(
                    $this->getFactory()->getUniqueProperties(),
                    (array)$this->getFactory()->getTable()->getPrimaryKey()
                )
            )
        );
    }

    /**
     * @return bool
     */
    public function isInPersistMode(): bool
    {
        return self::$inPersistMode;
    }

    /**
     * @return void
     */
    public function startPersistMode(): void
    {
        self::$inPersistMode = true;
    }

    /**
     * @return void
     */
    public function endPersistMode(): void
    {
        self::$inPersistMode = false;
    }

    /**
     * @return array
     */
    public function getEnforcedFields(): array
    {
        return $this->enforcedFields;
    }

    /**
     * When a field is set in the factory instantiation
     * or in a patchData, save the name of the fields that
     * have been set by the user. This is useful for the
     * uniqueness of the fields.
     *
     * @param array $fields Fields to be marked as enforced.
     * @return void
     */
    public function addEnforcedFields(array $fields)
    {
        $this->enforcedFields = array_merge(
            array_keys($fields),
            $this->enforcedFields
        );
    }
}
