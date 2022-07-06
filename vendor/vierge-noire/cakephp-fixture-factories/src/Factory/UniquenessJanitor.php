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

use Cake\Utility\Hash;
use CakephpFixtureFactories\Error\UniquenessException;

/**
 * Class UniquenessJanitor
 *
 * @internal
 */
class UniquenessJanitor
{
    /**
     * When providing data to a factory, unique fields are scanned
     * in order to warn the user that she is about to create duplicates.
     *
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Factory on which the entity will be built.
     * @param \Cake\Datasource\EntityInterface[] $entities Array of data meant to be patched into entities.
     * @param bool $isStrict Throw an exception if unique fields in $entities collide.
     * @return \Cake\Datasource\EntityInterface[]
     * @throws \CakephpFixtureFactories\Error\UniquenessException
     */
    public static function sanitizeEntityArray(BaseFactory $factory, array $entities, bool $isStrict = true): array
    {
        if (empty($factory->getUniqueProperties())) {
            return $entities;
        }

        $originalEntities = $entities;

        // Remove associated fields and non-unique fields
        foreach ($entities as &$entity) {
            $entity = $entity->setHidden([])->toArray();
            foreach ($entity as $k => $v) {
                if (is_array($v) || !in_array($k, $factory->getUniqueProperties())) {
                    unset($entity[$k]);
                }
            }
        }
        if (empty($entities)) {
            return $originalEntities;
        }

        $entities = Hash::flatten($entities);

        // Extract the key after the dot
        $getPropertyName = function (string $str): string {
            return substr($str, strrpos($str, '.') + 1);
        };

        // Extract the key before the dot
        $getIndex = function (string $str): int {
            return (int)substr($str, 0, strrpos($str, '.'));
        };

        $propertyIsUnique = function (string $property) use ($factory): bool {
            return in_array($property, array_merge(
                $factory->getUniqueProperties(),
                (array)$factory->getTable()->getPrimaryKey()
            ));
        };

        $indexesToRemove = [];
        foreach ($entities as $k1 => &$v1) {
            unset($entities[$k1]);
            if (empty($v1)) {
                continue;
            }
            $property = $getPropertyName($k1);
            foreach ($entities as $k2 => $v2) {
                if ($v1 == $v2 && $property === $getPropertyName($k2) && $propertyIsUnique($property)) {
                    if ($isStrict) {
                        $factoryName = get_class($factory);
                        throw new UniquenessException(
                            "Error in {$factoryName}. The uniqueness of {$property} was not respected."
                        );
                    } else {
                        $indexesToRemove[] = $getIndex($k2);
                    }
                }
            }
        }
        foreach (array_unique($indexesToRemove) as $i) {
            unset($originalEntities[$i]);
        }

        return array_values($originalEntities);
    }
}
