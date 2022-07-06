<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * MachineFactory
 *
 * @method \App\Model\Entity\Machine getEntity()
 * @method \App\Model\Entity\Machine[] getEntities()
 * @method \App\Model\Entity\Machine|\App\Model\Entity\Machine[] persist()
 * @method static \App\Model\Entity\Machine get(mixed $primaryKey, array $options = [])
 */
class MachineFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'Machines';
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
                'machine_name' => $faker->name,
                'is_disabled' => $faker->boolean,
            ];
        })
            ->withSector(1);
    }

    public function withSector($parameter = null, int $n = 1): self
    {
        return $this->with('Sectors', SectorFactory::make($parameter, $n));
    }
}
