<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * SubCategoryFactory
 *
 * @method \App\Model\Entity\SubCategory getEntity()
 * @method \App\Model\Entity\SubCategory[] getEntities()
 * @method \App\Model\Entity\SubCategory|\App\Model\Entity\SubCategory[] persist()
 * @method static \App\Model\Entity\SubCategory get(mixed $primaryKey, array $options = [])
 */
class SubCategoryFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'SubCategories';
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
                'name' => $faker->name,
                'column_na' => $faker->boolean,
            ];
        })
            ->withCategory(1);
    }

    public function withCategory($parameter = null, int $n = 1): self
    {
        return $this->with('Categories', CategoryFactory::make($parameter, $n));
    }
}
