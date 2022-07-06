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

use Cake\Datasource\EntityInterface;
use Cake\I18n\I18n;
use Cake\ORM\Query;
use Cake\ORM\Table;
use CakephpFixtureFactories\Error\FixtureFactoryException;
use CakephpFixtureFactories\Error\PersistenceException;
use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use function array_merge;
use function is_array;
use function is_callable;

/**
 * Class BaseFactory
 *
 * @package CakephpFixtureFactories\Factory
 */
abstract class BaseFactory
{
    /**
     * @var \Faker\Generator|null
     */
    private static $faker;
    /**
     * @deprecated
     * @var bool
     */
    protected static $applyListenersAndBehaviors = false;
    /**
     * @var array
     */
    protected $marshallerOptions = [
        'validate' => false,
        'forceNew' => true,
        'accessibleFields' => ['*' => true],
    ];
    /**
     * @var array
     */
    protected $saveOptions = [
        'checkRules' => false,
        'atomic' => false,
        'checkExisting' => false,
    ];
    /**
     * @var array Unique fields. Uniqueness applies only to persisted entities.
     */
    protected $uniqueProperties = [];
    /**
     * The number of records the factory should create
     *
     * @var int
     */
    private $times = 1;
    /**
     * The data compiler gathers the data from the
     * default template, the injection and patched data
     * and compiles it to produce the data feeding the
     * entities of the Factory
     *
     * @var \CakephpFixtureFactories\Factory\DataCompiler
     */
    private $dataCompiler;
    /**
     * Helper to check and build data in associations
     *
     * @var \CakephpFixtureFactories\Factory\AssociationBuilder
     */
    private $associationBuilder;
    /**
     * Handles the events at the model and behavior level
     * for the table on which the factories will be built
     *
     * @var \CakephpFixtureFactories\Factory\EventCollector
     */
    private $eventCompiler;

    /**
     * BaseFactory constructor.
     */
    final protected function __construct()
    {
        $this->dataCompiler = new DataCompiler($this);
        $this->associationBuilder = new AssociationBuilder($this);
        $this->eventCompiler = new EventCollector($this->getRootTableRegistryName());
    }

    /**
     * Table Registry the factory is building entities from
     *
     * @return string
     */
    abstract protected function getRootTableRegistryName(): string;

    /**
     * @return void
     */
    abstract protected function setDefaultTemplate(): void;

    /**
     * @param array|callable|null|int|\Cake\Datasource\EntityInterface $makeParameter Injected data
     * @param int                     $times Number of entities created
     * @return static
     */
    public static function make($makeParameter = [], int $times = 1): BaseFactory
    {
        if (is_numeric($makeParameter)) {
            $factory = static::makeFromNonCallable();
            $times = $makeParameter;
        } elseif (is_null($makeParameter)) {
            $factory = static::makeFromNonCallable();
        } elseif (is_array($makeParameter) || $makeParameter instanceof EntityInterface) {
            $factory = static::makeFromNonCallable($makeParameter);
        } elseif (is_callable($makeParameter)) {
            $factory = static::makeFromCallable($makeParameter);
        } else {
            throw new InvalidArgumentException('
                ::make only accepts an array, an integer, an EntityInterface or a callable as first parameter.
            ');
        }

        $factory->setUp($factory, $times);

        return $factory;
    }

    /**
     * Collect the number of entities to be created
     * Apply the default template in the factory
     *
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Factory
     * @param int         $times Number of entities created
     * @return void
     */
    protected function setUp(BaseFactory $factory, int $times): void
    {
        $factory->initialize();
        $factory->setTimes($times);
        $factory->setDefaultTemplate();
        $factory->getDataCompiler()->collectAssociationsFromDefaultTemplate();
    }

    /**
     * This method may be used to define associations
     * missing in your model but useful to build factories
     *
     * @return void
     */
    protected function initialize(): void
    {
        // Add logic prior to generating the default template.
    }

    /**
     * @param array|\Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[] $data Injected data
     * @return static
     */
    private static function makeFromNonCallable($data = []): BaseFactory
    {
        $factory = new static();
        $factory->getDataCompiler()->collectFromInstantiation($data);

        return $factory;
    }

    /**
     * @param callable $fn Injected data
     * @return static
     */
    private static function makeFromCallable(callable $fn): BaseFactory
    {
        $factory = new static();
        $factory->getDataCompiler()->collectArrayFromCallable($fn);

        return $factory;
    }

    /**
     * Faker's local is set as the I18n local.
     * If not supported by Faker, take faker's default.
     *
     * @return \Faker\Generator
     */
    public function getFaker(): Generator
    {
        if (is_null(self::$faker)) {
            try {
                $fakerLocale = I18n::getLocale();
                $faker = Factory::create($fakerLocale);
            } catch (\Throwable $e) {
                $fakerLocale = Factory::DEFAULT_LOCALE;
                $faker = Factory::create($fakerLocale);
            }
            $faker->seed(1234);
            self::$faker = $faker;
        }

        return self::$faker;
    }

    /**
     * Produce one entity from the present factory
     *
     * @return \Cake\Datasource\EntityInterface
     */
    public function getEntity(): EntityInterface
    {
        return $this->toArray()[0];
    }

    /**
     * Produce a set of entities from the present factory
     *
     * @return \Cake\Datasource\EntityInterface[]
     */
    public function getEntities(): array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function getMarshallerOptions(): array
    {
        $associated = $this->getAssociationBuilder()->getAssociated();
        if (!empty($associated)) {
            return array_merge($this->marshallerOptions, [
                'associated' => $this->getAssociationBuilder()->getAssociated(),
            ]);
        } else {
            return $this->marshallerOptions;
        }
    }

    /**
     * @return array
     */
    public function getAssociated(): array
    {
        return $this->getAssociationBuilder()->getAssociated();
    }

    /**
     * Fetch entities from the data compiler.
     *
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function toArray(): array
    {
        $entities = [];
        for ($i = 0; $i < $this->times; $i++) {
            $compiledData = $this->getDataCompiler()->getCompiledTemplateData();
            if (is_array($compiledData)) {
                $entities = array_merge($entities, $compiledData);
            } else {
                $entities[] = $compiledData;
            }
        }
        UniquenessJanitor::sanitizeEntityArray($this, $entities);

        return $entities;
    }

    /**
     * The table on which the factories are build, the package's one
     *
     * @return \Cake\ORM\Table
     */
    public function getTable(): Table
    {
        return $this->getEventCompiler()->getTable();
    }

    /**
     * @return \Cake\Datasource\EntityInterface|array<\Cake\Datasource\EntityInterface>|\Cake\Datasource\ResultSetInterface
     * @throws \CakephpFixtureFactories\Error\PersistenceException if the entity/entities could not be saved.
     */
    public function persist()
    {
        $this->getDataCompiler()->startPersistMode();
        $entities = $this->toArray();
        $this->getDataCompiler()->endPersistMode();

        try {
            if (count($entities) === 1) {
                return $this->getTable()->saveOrFail($entities[0], $this->getSaveOptions());
            } else {
                return $this->getTable()->saveManyOrFail($entities, $this->getSaveOptions());
            }
        } catch (\Throwable $exception) {
            $factory = static::class;
            $message = $exception->getMessage();
            throw new PersistenceException("Error in Factory $factory.\n Message: $message \n");
        }
    }

    /**
     * @return array
     */
    private function getSaveOptions(): array
    {
        return array_merge($this->saveOptions, [
            'associated' => $this->getAssociated(),
        ]);
    }

    /**
     * Assigns the values of $data to the $keys of the entities generated
     *
     * @param array|\Cake\Datasource\EntityInterface $data Data to inject
     * @return $this
     */
    public function patchData($data)
    {
        if ($data instanceof EntityInterface) {
            $data = $data->toArray();
        }
        $this->getDataCompiler()->collectFromPatch($data);

        return $this;
    }

    /**
     * Sets the value for a single field
     *
     * @param string $field to set
     * @param mixed $value to assign
     * @return $this
     */
    public function setField(string $field, $value)
    {
        return $this->patchData([$field => $value]);
    }

    /**
     * A protected class dedicated to generating / collecting data for this factory
     *
     * @return \CakephpFixtureFactories\Factory\DataCompiler
     */
    protected function getDataCompiler(): DataCompiler
    {
        return $this->dataCompiler;
    }

    /**
     * A protected class dedicated to building / collecting associations for this factory
     *
     * @return \CakephpFixtureFactories\Factory\AssociationBuilder
     */
    protected function getAssociationBuilder(): AssociationBuilder
    {
        return $this->associationBuilder;
    }

    /**
     * A protected class to manage the Model Events inhrent to the creation of fixtures
     *
     * @return \CakephpFixtureFactories\Factory\EventCollector
     */
    protected function getEventCompiler(): EventCollector
    {
        return $this->eventCompiler;
    }

    /**
     * Get the amount of entities generated by the factory
     *
     * @return int
     */
    public function getTimes(): int
    {
        return $this->times;
    }

    /**
     * Set the amount of entities generated by the factory
     *
     * @param int $times Number if entities created
     * @return self
     */
    public function setTimes(int $times): self
    {
        $this->times = $times;

        return $this;
    }

    /**
     * @param string[]|string $activeBehaviors Behaviors listened to by the factory
     * @return self
     * @throws \CakephpFixtureFactories\Error\FixtureFactoryException on argument passed error
     */
    public function listeningToBehaviors($activeBehaviors): self
    {
        $activeBehaviors = (array)$activeBehaviors;
        if (empty($activeBehaviors)) {
            throw new FixtureFactoryException('Expecting a non empty string or an array of string.');
        }
        $this->getEventCompiler()->listeningToBehaviors($activeBehaviors);

        return $this;
    }

    /**
     * @param string[]|string $activeModelEvents Model events listened to by the factory
     * @return self
     * @throws \CakephpFixtureFactories\Error\FixtureFactoryException on argument passed error
     */
    public function listeningToModelEvents($activeModelEvents): self
    {
        $activeModelEvents = (array)$activeModelEvents;
        if (empty($activeModelEvents)) {
            throw new FixtureFactoryException('Expecting a non empty string or an array of string.');
        }
        $this->getEventCompiler()->listeningToModelEvents($activeModelEvents);

        return $this;
    }

    /**
     * Set an offset for the Ids of the entities
     * persisted by this factory. This can be an array of type
     * [
     *      composite_key_1 => value1,
     *      composite_key_2 => value2,
     *      ...
     * ]
     * If not set, the offset is set randomly
     *
     * @param int|string|array $primaryKeyOffset Offset
     * @return self
     */
    public function setPrimaryKeyOffset($primaryKeyOffset): self
    {
        $this->getDataCompiler()->setPrimaryKeyOffset($primaryKeyOffset);

        return $this;
    }

    /**
     * Get the fields that are declared are unique.
     * This should include the uniqueness of the fields in your schema.
     *
     * @return array
     */
    public function getUniqueProperties(): array
    {
        return $this->uniqueProperties;
    }

    /**
     * Set the unique fields of the factory.
     * If a field is unique and explicitly modified,
     * it's existence will be checked
     * before persisting. If found, no new
     * entity will be created, but instead the
     * existing one will be considered.
     *
     * @param array|string|null $fields Unique fields set on the fly.
     * @return $this
     */
    public function setUniqueProperties($fields)
    {
        $this->uniqueProperties = (array)$fields;

        return $this;
    }

    /**
     * Populate the entity factored
     *
     * @param callable $fn Callable delivering injected data
     * @return $this
     */
    protected function setDefaultData(callable $fn)
    {
        $this->getDataCompiler()->collectFromDefaultTemplate($fn);

        return $this;
    }

    /**
     * Add associated entities to the fixtures generated by the factory
     * The associated name can be of several depth, dot separated
     * The data can be an array, an integer, an entity interface, a callable or a factory
     *
     * @param string $associationName Association name
     * @param array|int|callable|\CakephpFixtureFactories\Factory\BaseFactory|\Cake\Datasource\EntityInterface $data Injected data
     * @return $this
     */
    public function with(string $associationName, $data = [])
    {
        $this->getAssociationBuilder()->getAssociation($associationName);

        if (strpos($associationName, '.') === false && $data instanceof BaseFactory) {
            $factory = $data;
        } else {
            $factory = $this->getAssociationBuilder()->getAssociatedFactory($associationName, $data);
        }

        // Extract the first Association in the string
        $associationName = strtok($associationName, '.');

        // Remove the brackets in the association
        $associationName = $this->getAssociationBuilder()->removeBrackets($associationName);

        $isToOne = $this->getAssociationBuilder()->processToOneAssociation($associationName, $factory);
        $this->getDataCompiler()->collectAssociation($associationName, $factory, $isToOne);

        $this->getAssociationBuilder()->collectAssociatedFactory($associationName, $factory);

        return $this;
    }

    /**
     * Unset a previously associated factory
     * Useful to bypass associations set in setDefaultTemplate
     *
     * @param string $association Association name
     * @return $this
     */
    public function without(string $association)
    {
        $this->getDataCompiler()->dropAssociation($association);
        $this->getAssociationBuilder()->dropAssociation($association);

        return $this;
    }

    /**
     * @param array $data Data to merge
     * @return $this
     */
    public function mergeAssociated(array $data)
    {
        $this->getAssociationBuilder()->setAssociated(
            array_merge(
                $this->getAssociationBuilder()->getAssociated(),
                $data
            )
        );

        return $this;
    }

    /**
     * Query the factory's related table without before find.
     *
     * @param string $type the type of query to perform
     * @param array $options An array that will be passed to Query::applyOptions()
     * @return \Cake\ORM\Query The query builder
     * @see Query::find()
     */
    public static function find(string $type = 'all', array $options = []): Query
    {
        return (new static())->getTable()->find($type, $options);
    }

    /**
     * Get from primary key the factory's related table entries, without before find.
     *
     * @param mixed $primaryKey primary key value to find
     * @param array $options options accepted by `Table::find()`
     * @return \Cake\Datasource\EntityInterface
     * @see Table::get()
     */
    public static function get($primaryKey, array $options = []): EntityInterface
    {
        return (new static())->getTable()->get($primaryKey, $options);
    }

    /**
     * Count the factory's related table entries without before find.
     *
     * @return int
     * @see Query::count()
     */
    public static function count(): int
    {
        return self::find()->count();
    }
}
