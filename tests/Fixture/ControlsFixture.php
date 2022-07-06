<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ControlsFixture
 */
class ControlsFixture extends TestFixture
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
                'created' => '2022-05-06 11:45:46',
                'modified' => '2022-05-06 11:45:46',
                'date_time' => '2022-05-06 11:45:46',
                'checklist_id' => 1,
            ],
        ];
        parent::init();
    }
}
