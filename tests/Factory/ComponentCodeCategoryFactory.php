<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * ComponentCodeCategoryFactory
 *
 * @method \App\Model\Entity\ComponentCodeCategory getEntity()
 * @method \App\Model\Entity\ComponentCodeCategory[] getEntities()
 * @method \App\Model\Entity\ComponentCodeCategory|\App\Model\Entity\ComponentCodeCategory[] persist()
 * @method static \App\Model\Entity\ComponentCodeCategory get(mixed $primaryKey, array $options = [])
 */
class ComponentCodeCategoryFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'ComponentCodeCategories';
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
                'component_code' => $faker->name,
            ];
        })
            ->withCategory(1);
    }

    public function withCategory($parameter = null, int $n = 1): self
    {
        return $this->with('Categories', CategoryFactory::make($parameter, $n));
    }
}
