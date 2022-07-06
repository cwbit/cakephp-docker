<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * CustomUserFactory
 *
 * @method \Cake\ORM\Entity getEntity()
 * @method \Cake\ORM\Entity[] getEntities()
 * @method \Cake\ORM\Entity|\Cake\ORM\Entity[] persist()
 * @method static \Cake\ORM\Entity get(mixed $primaryKey, array $options = [])
 */
class CustomUserFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'CustomUsers';
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
                'username' => $faker->name,
                'email' => $faker->email,
                'password' => 'test',
                'first_name' => 'prénom',
                'last_name' => 'nom',
                'active' => true,
                'role' => 'admin',
                'is_superuser' => false,
            ];
        });
    }
}
