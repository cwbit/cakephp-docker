<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * ControlsCategoryFactory
 *
 * @method \App\Model\Entity\ControlsCategory getEntity()
 * @method \App\Model\Entity\ControlsCategory[] getEntities()
 * @method \App\Model\Entity\ControlsCategory|\App\Model\Entity\ControlsCategory[] persist()
 * @method static \App\Model\Entity\ControlsCategory get(mixed $primaryKey, array $options = [])
 */
class ControlsCategoryFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'ControlsCategories';
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
                'code_category' => $faker->numberBetween,
            ];
        })
            ->withControl(1);
    }

    public function withControl($parameter = null, int $n = 1): self
    {
        return $this->with('Controls', ControlFactory::make($parameter, $n));
    }
}
