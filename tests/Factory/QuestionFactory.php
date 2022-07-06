<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * QuestionFactory
 *
 * @method \App\Model\Entity\Question getEntity()
 * @method \App\Model\Entity\Question[] getEntities()
 * @method \App\Model\Entity\Question|\App\Model\Entity\Question[] persist()
 * @method static \App\Model\Entity\Question get(mixed $primaryKey, array $options = [])
 */
class QuestionFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'Questions';
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
                'entitled' => $faker->name,
                'color' => 'red',
                'code_key' => 'cc_red',
                'unity' => 'milimeter',
                'corrective_action' => 'azeaz',
                'leader_alert' => 1,
                'is_value_required' => 1,
                'is_disabled' => 1,
                'column_na' => $faker->boolean,
            ];
        })
            ->withSubCategory(1);
    }

    public function withSubCategory($parameter = null, int $n = 1): self
    {
        return $this->with('SubCategories', SubCategoryFactory::make($parameter, $n));
    }
}
