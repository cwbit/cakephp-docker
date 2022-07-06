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
namespace CakephpTestSuiteLight\Test\TestCase\Sniffer;


use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use CakephpTestSuiteLight\Fixture\TruncateDirtyTables;
use CakephpTestSuiteLight\Sniffer\BaseTableSniffer;
use CakephpTestSuiteLight\Sniffer\SnifferRegistry;
use CakephpTestSuiteLight\Test\Traits\ArrayComparerTrait;
use CakephpTestSuiteLight\Test\Traits\ExpectedSchemaTestTrait;
use CakephpTestSuiteLight\Test\Traits\InsertTestDataTrait;
use CakephpTestSuiteLight\Test\Traits\SnifferHelperTrait;
use TestApp\Model\Table\CitiesTable;
use TestApp\Model\Table\CountriesTable;

class TableSnifferWithoutFixturesTest extends TestCase
{
    use ArrayComparerTrait;
    use ExpectedSchemaTestTrait;
    use InsertTestDataTrait;
    use SnifferHelperTrait;
    use TruncateDirtyTables;

    /**
     * @var BaseTableSniffer
     */
    public $TableSniffer;

    /**
     * @var CountriesTable
     */
    public $Countries;

    /**
     * @var CitiesTable
     */
    public $Cities;

    public function setUp(): void
    {
        parent::setUp();
        $this->TableSniffer = SnifferRegistry::get('test');
        $this->Countries = TableRegistry::getTableLocator()->get('Countries');
        $this->Cities = TableRegistry::getTableLocator()->get('Cities');

        $this->activateForeignKeysOnSqlite();
    }

    public function tearDown(): void
    {
        unset($this->TableSniffer);
        unset($this->Countries);
        unset($this->Cities);
        ConnectionManager::drop('test_dummy_connection');

        parent::tearDown();
    }

    /**
     * Find dirty tables
     * Countries is dirty, Cities is empty
     */
    public function testGetDirtyTables()
    {
        $expected = [
            'countries',
        ];

        $this->createCountry();
        $found = $this->TableSniffer->getDirtyTables();
        $this->assertArraysHaveSameContent($expected, $found);
    }
}
