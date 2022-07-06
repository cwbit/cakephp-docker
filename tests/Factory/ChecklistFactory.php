<?php
declare(strict_types=1);

namespace App\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * ChecklistFactory
 *
 * @method \App\Model\Entity\Checklist getEntity()
 * @method \App\Model\Entity\Checklist[] getEntities()
 * @method \App\Model\Entity\Checklist|\App\Model\Entity\Checklist[] persist()
 * @method static \App\Model\Entity\Checklist get(mixed $primaryKey, array $options = [])
 */
class ChecklistFactory extends CakephpBaseFactory
{
    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'Checklists';
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
                'checklist_name' => $faker->name,
                'validated_at' => $faker->dateTime(),
                'parent_checklist_id' => 1,
                'author_id' => $faker->regexify('[A-Z]{5}[0-4]{3}'),
                'version' => 1,
            ];
        })
            ->withMachine(1);
    }

    public function withMachine($parameter = null, int $n = 1): self
    {
        return $this->with('Machines', MachineFactory::make($parameter, $n));
    }
}
