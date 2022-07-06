<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * SectorFactory
 *
 * @method \App\Model\Entity\Sector getEntity()
 * @method \App\Model\Entity\Sector[] getEntities()
 * @method \App\Model\Entity\Sector|\App\Model\Entity\Sector[] persist()
 * @method static \App\Model\Entity\Sector get(mixed $primaryKey, array $options = [])
 */
class SectorFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'Sectors';
    }

    /**
     * Defines the factory's default values. This is useful for
     * not nullable fields. You may use methods of the present factory here too.
     *
     * @return void
     */
    protected function setDefaultTemplate(): void
    {
        $this->setDefaultData(function (Generator $faker) {
            return [
                'sector_name' => $faker->name,
            ];
        });
    }
}
