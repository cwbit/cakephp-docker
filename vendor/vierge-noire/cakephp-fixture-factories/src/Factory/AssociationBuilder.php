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

use Cake\ORM\Association;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use CakephpFixtureFactories\Error\AssociationBuilderException;

/**
 * Class AssociationBuilder
 *
 * @internal
 */
class AssociationBuilder
{
    use FactoryAwareTrait {
        getFactory as getFactoryInstance;
    }

    private $associated = [];

    /**
     * @var \CakephpFixtureFactories\Factory\BaseFactory
     */
    private $factory;

    /**
     * AssociationBuilder constructor.
     *
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Associated factory
     */
    public function __construct(BaseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Makes sure that a given association is well defined in the
     * builder's factory's table
     *
     * @param string $associationName Name of the association
     * @return \Cake\ORM\Association
     */
    public function getAssociation(string $associationName): Association
    {
        $this->removeBrackets($associationName);

        try {
            $association = $this->getTable()->getAssociation($associationName);
        } catch (\Exception $e) {
            throw new AssociationBuilderException($e->getMessage());
        }
        if ($this->associationIsToOne($association) || $this->associationIsToMany($association)) {
            return $association;
        } else {
            $associationType = get_class($association);
            throw new AssociationBuilderException(
                "Unknown association type $associationType on table {$this->getTable()->getAlias()}"
            );
        }
    }

    /**
     * Collect an associated factory to the BaseFactory
     *
     * @param string $associationName Association
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Factory
     * @return void
     */
    public function collectAssociatedFactory(string $associationName, BaseFactory $factory): void
    {
        $associations = $this->getAssociated();

        if (!in_array($associationName, $associations)) {
            $associations[$associationName] = $factory->getMarshallerOptions();
        }

        $this->setAssociated($associations);
    }

    /**
     * @param string      $associationName Name of the association
     * @param \CakephpFixtureFactories\Factory\BaseFactory $associationFactory Factory
     * @return bool
     */
    public function processToOneAssociation(string $associationName, BaseFactory $associationFactory): bool
    {
        $this->validateToOneAssociation($associationName, $associationFactory);
        $this->removeAssociationForToOneFactory($associationName, $associationFactory);

        return $this->associationIsToOne($this->getAssociation($associationName));
    }

    /**
     * HasOne and BelongsTo association cannot be multiple
     *
     * @param string $associationName Name of the association
     * @param \CakephpFixtureFactories\Factory\BaseFactory $associationFactory Factory
     * @return bool
     */
    public function validateToOneAssociation(string $associationName, BaseFactory $associationFactory): bool
    {
        if ($this->associationIsToOne($this->getAssociation($associationName)) && $associationFactory->getTimes() > 1) {
            throw new AssociationBuilderException(
                "Association $associationName on " . $this->getTable()->getEntityClass() . ' cannot be multiple'
            );
        }

        return true;
    }

    /**
     * @param string      $associationName Association name
     * @param \CakephpFixtureFactories\Factory\BaseFactory $associatedFactory Factory
     * @return void
     */
    public function removeAssociationForToOneFactory(string $associationName, BaseFactory $associatedFactory): void
    {
        if ($this->associationIsToMany($this->getAssociation($associationName))) {
            $associatedAssociationName = Inflector::singularize($this->getTable()->getRegistryAlias());
            if ($associatedFactory->getTable()->hasAssociation($associatedAssociationName)) {
                $associatedFactory->without($associatedAssociationName);
            }
        }
    }

    /**
     * Get the factory for the association
     *
     * @param string $associationName Association name
     * @param scalar[]|\CakephpFixtureFactories\Factory\BaseFactory|\Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[] $data Injected data
     * @return \CakephpFixtureFactories\Factory\BaseFactory
     */
    public function getAssociatedFactory(string $associationName, $data = []): BaseFactory
    {
        $associations = explode('.', $associationName);
        $firstAssociation = array_shift($associations);

        $times = $this->getTimeBetweenBrackets($firstAssociation);
        $this->removeBrackets($firstAssociation);

        $table = $this->getTable()->getAssociation($firstAssociation)->getClassName();

        if (!empty($associations)) {
            $factory = $this->getFactoryFromTableName($table);
            $factory->with(implode('.', $associations), $data);
        } else {
            $factory = $this->getFactoryFromTableName($table, $data);
        }
        if ($times) {
            $factory->setTimes($times);
        }

        return $factory;
    }

    /**
     * Get a factory from a table name
     *
     * @param string $modelName Model Name
     * @param array $data Injected data
     * @return \CakephpFixtureFactories\Factory\BaseFactory
     */
    public function getFactoryFromTableName(string $modelName, $data = []): BaseFactory
    {
        try {
            return $this->getFactoryInstance($modelName, $data);
        } catch (\Throwable $e) {
            throw new AssociationBuilderException($e->getMessage());
        }
    }

    /**
     * Remove the brackets and there content in a n 'Association1[i].Association2[j]' formatted string
     *
     * @param string $string String
     * @return string
     */
    public function removeBrackets(string &$string): string
    {
        return $string = preg_replace("/\[[^]]+\]/", '', $string);
    }

    /**
     * Return the integer i between brackets in an 'Association[i]' formatted string
     *
     * @param string $string String
     * @return int|null
     */
    public function getTimeBetweenBrackets(string $string)
    {
        preg_match_all("/\[([^\]]*)\]/", $string, $matches);
        $res = $matches[1];
        if (empty($res)) {
            return null;
        } elseif (count($res) === 1 && !empty($res[0])) {
            return (int)$res[0];
        } else {
            throw new AssociationBuilderException("Error parsing $string.");
        }
    }

    /**
     * @return \CakephpFixtureFactories\Factory\BaseFactory Factory
     */
    public function getFactory(): BaseFactory
    {
        return $this->factory;
    }

    /**
     * @param \Cake\ORM\Association $association Association
     * @return bool
     */
    public function associationIsToOne(Association $association): bool
    {
        return $association instanceof HasOne || $association instanceof BelongsTo;
    }

    /**
     * @param \Cake\ORM\Association $association Association
     * @return bool
     */
    public function associationIsToMany(Association $association): bool
    {
        return $association instanceof HasMany || $association instanceof BelongsToMany;
    }

    /**
     * Scan for all associations starting by the $association path provided and drop them
     *
     * @param string $associationName Association name
     * @return void
     */
    public function dropAssociation(string $associationName): void
    {
        $this->setAssociated(
            Hash::remove(
                $this->getAssociated(),
                $associationName
            )
        );
    }

    /**
     * @return array
     */
    public function getAssociated(): array
    {
        return $this->associated;
    }

    /**
     * @param array $associated Associations of the master factory
     * @return void
     */
    public function setAssociated(array $associated): void
    {
        $this->associated = $associated;
    }

    /**
     * @return \Cake\ORM\Table
     */
    public function getTable(): Table
    {
        return $this->getFactory()->getTable();
    }
}
