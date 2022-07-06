<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ControlsCategoriesFixture
 */
class ControlsCategoriesFixture extends TestFixture
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
                'control_id' => 1,
                'code_category' => 1,
                'created' => '2022-05-13 14:10:30',
                'modified' => '2022-05-13 14:10:30',
            ],
        ];
        parent::init();
    }
}
