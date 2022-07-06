<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * QuestionsFixture
 */
class QuestionsFixture extends TestFixture
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
                'subCategory_id' => 1,
                'entitled' => 'Lorem ipsum dolor sit amet',
                'color' => 'Lorem ipsum dolor sit amet',
                'created' => '2022-05-03 12:39:34',
                'modified' => '2022-05-03 12:39:34',
                'code_key' => 'Lorem ipsum dolor sit amet',
                'unity' => 'Lorem ipsum dolor sit amet',
                'corrective_action' => 'Lorem ipsum dolor sit amet',
                'leader_alert' => 1,
                'is_value_required' => 1,
                'is_disabled' => 1,
            ],
        ];
        parent::init();
    }
}
