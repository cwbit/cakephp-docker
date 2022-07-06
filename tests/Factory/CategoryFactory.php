<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * CategoryFactory
 *
 * @method \App\Model\Entity\Category getEntity()
 * @method \App\Model\Entity\Category[] getEntities()
 * @method \App\Model\Entity\Category|\App\Model\Entity\Category[] persist()
 * @method static \App\Model\Entity\Category get(mixed $primaryKey, array $options = [])
 */
class CategoryFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'Categories';
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
                'category_name' => $faker->name,
                'component_code' => 1,
                'column_na' => true,
                'security' => true,
                'is_disabled' => $faker->boolean,
            ];
        })
            ->withChecklist(1);
    }

    public function withChecklist($parameter = null, int $n = 1): self
    {
        return $this->with('Checklists', ChecklistFactory::make($parameter, $n));
    }
}
