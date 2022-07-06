<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SubCategoriesFixture
 */
class SubCategoriesFixture extends TestFixture
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
                'name' => 'Lorem ipsum dolor sit amet',
                'category_id' => 1,
                'created' => '2022-05-03 11:32:13',
                'modified' => '2022-05-03 11:32:13',
            ],
        ];
        parent::init();
    }
}
