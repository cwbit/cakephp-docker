<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ResponsesFixture
 */
class ResponsesFixture extends TestFixture
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
                'question_id' => 1,
                'is_conform' => 1,
                'response_value' => 'Lorem ipsum dolor sit amet',
                'response_statut' => 1,
                'created' => '2022-05-09 09:02:25',
                'modified' => '2022-05-09 09:02:25',
            ],
        ];
        parent::init();
    }
}
