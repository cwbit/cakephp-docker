<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * user_sectorFactory
 *
 * @method \Cake\ORM\Entity getEntity()
 * @method \Cake\ORM\Entity[] getEntities()
 * @method \Cake\ORM\Entity|\Cake\ORM\Entity[] persist()
 * @method static \Cake\ORM\Entity get(mixed $primaryKey, array $options = [])
 */
class UserSectorFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'user_sectors';
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
                'sector_id' => $faker->number,
            ];
        })->withUser(1);
    }

    public function withUser($parameter = null, int $n = 1): self
    {
        return $this->with('Users', CustomUserFactory::make($parameter, $n));
    }
}
