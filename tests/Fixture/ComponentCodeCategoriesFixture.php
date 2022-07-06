<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ComponentCodeCategoriesFixture
 */
class ComponentCodeCategoriesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'category_id' => 1,
                'component_code' => 'Lorem ipsum dolor sit amet',
                'created' => '2022-06-16 13:46:21',
                'modified' => '2022-06-16 13:46:21',
            ],
        ];
        parent::init();
    }
}
