<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UserSectorsFixture
 */
class UserSectorsFixture extends TestFixture
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
                'user_id' => 1,
                'sector_id' => 1,
                'created' => '2022-06-01 12:26:38',
                'modified' => '2022-06-01 12:26:38',
            ],
        ];
        parent::init();
    }
}
