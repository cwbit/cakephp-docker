<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * ControlFactory
 *
 * @method \App\Model\Entity\Control getEntity()
 * @method \App\Model\Entity\Control[] getEntities()
 * @method \App\Model\Entity\Control|\App\Model\Entity\Control[] persist()
 * @method static \App\Model\Entity\Control get(mixed $primaryKey, array $options = [])
 */
class ControlFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'Controls';
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
                'date_time' => $faker->dateTime(),
                'is_valid' => false,
            ];
        })
            ->withChecklist(1);
    }

    public function withChecklist($parameter = null, int $n = 1): self
    {
        return $this->with('Checklists', ChecklistFactory::make($parameter, $n));
    }
}
