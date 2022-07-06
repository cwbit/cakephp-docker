<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * ResponseFactory
 *
 * @method \App\Model\Entity\Response getEntity()
 * @method \App\Model\Entity\Response[] getEntities()
 * @method \App\Model\Entity\Response|\App\Model\Entity\Response[] persist()
 * @method static \App\Model\Entity\Response get(mixed $primaryKey, array $options = [])
 */
class ResponseFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'Responses';
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
                'is_conform' => 1,
                'response_value' => $faker->paragraph,
                'response_statut' => 1,
            ];
        })
            ->withControl(1)
            ->withQuestion(1);
    }

    public function withControl($parameter = null, int $n = 1): self
    {
        return $this->with('Controls', ControlFactory::make($parameter, $n));
    }

    public function withQuestion($parameter = null, int $n = 1): self
    {
        return $this->with('Questions', QuestionFactory::make($parameter, $n));
    }
}
