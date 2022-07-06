<?php
declare(strict_types=1);

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2020 Juan Pablo Ramirez and Nicolas Masson
 * @link          https://webrider.de/
 * @since         1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace TestApp\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CitiesFixture extends TestFixture
{
    public function init(): void
    {
        $this->records = [
            [
                'uuid_primary_key' => '123e4567-e89b-12d3-a456-555723848771',
                'name' => 'First City',
                'country_id' => 1,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ],
        ];
        parent::init();
    }
}
