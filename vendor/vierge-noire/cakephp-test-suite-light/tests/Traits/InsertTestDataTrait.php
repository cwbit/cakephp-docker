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
namespace CakephpTestSuiteLight\Test\Traits;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use CakephpTestSuiteLight\Test\TestUtil;

trait InsertTestDataTrait
{
    private function createCountry(): EntityInterface
    {
        $Countries = TableRegistry::getTableLocator()->get('Countries');
        $country = $Countries->newEntity([
            'name' => 'Foo Country',
        ]);
        return $Countries->saveOrFail($country);
    }

    private function createCity(): EntityInterface
    {
        $Cities = TableRegistry::getTableLocator()->get('Cities');
        $city = $Cities->newEntity([
            'uuid_primary_key' => TestUtil::makeUuid(),
            'id_primary_key' => rand(1, 99999999),
            'name' => 'Foo City',
            'country_id' => $this->createCountry()->id
        ]);
        return $Cities->saveOrFail($city);
    }
}
