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
use Cake\TestSuite\ConnectionHelper;
use Cake\TestSuite\TestCase;
use CakephpTestSuiteLight\Fixture\TruncateDirtyTables;
use CakephpTestSuiteLight\Sniffer\BaseTableSniffer;
use CakephpTestSuiteLight\Sniffer\BaseTriggerBasedTableSniffer;
use CakephpTestSuiteLight\Sniffer\SnifferRegistry;
use CakephpTestSuiteLight\Test\Traits\ArrayComparerTrait;
use CakephpTestSuiteLight\Test\Traits\ExpectedSchemaTestTrait;
use CakephpTestSuiteLight\Test\Traits\InsertTestDataTrait;
use CakephpTestSuiteLight\Test\Traits\SnifferHelperTrait;
use Migrations\Migrations;
use TestApp\Test\Fixture\CitiesFixture;
use TestApp\Test\Fixture\CountriesFixture;

class TableSnifferTest extends TestCase
{
    use ArrayComparerTrait;
    use ExpectedSchemaTestTrait;
    use InsertTestDataTrait;
    use SnifferHelperTrait;
    use TruncateDirtyTables;

    public $fixtures = [
        // The order here is important
        CountriesFixture::class,
        CitiesFixture::class,
    ];

    /**
     * @var BaseTriggerBasedTableSniffer
     */
    public $TableSniffer;

    public function setUp(): void
    {
        parent::setUp();
        $this->TableSniffer = SnifferRegistry::get('test');
    }

    public function tearDown(): void
    {
        unset($this->TableSniffer);

        ConnectionManager::drop('test_dummy_connection');

        parent::tearDown();
    }

    private function createNonExistentConnection()
    {
        $config = ConnectionManager::getConfig('test');
        $config['database'] = 'dummy_database';
        ConnectionManager::setConfig('test_dummy_connection', $config);
    }

    /**
     * All tables should be clean before every test
     */
    public function testGetDirtyTables()
    {
        $expected = $this->getAllDirtyTables();
        $this->assertArraysHaveSameContent($expected, $this->TableSniffer->getDirtyTables());
    }

    /**
     * If a DB is not created, the sniffers should not throw on exception.
     */
    public function testGetSnifferOnNonExistentDB()
    {
        $this->createNonExistentConnection();
        $sniffer = SnifferRegistry::get('test_dummy_connection');
        $this->assertInstanceOf(BaseTableSniffer::class, $sniffer);
    }

    public function testImplodeSpecial()
    {
        $array = ['foo', 'bar'];
        $glueBefore = 'ABC';
        $glueAfter = 'DEF';
        $expect = 'ABCfooDEFABCbarDEF';
        $this->assertSame($expect, $this->TableSniffer->implodeSpecial($glueBefore, $array, $glueAfter));
    }

    public function testCheckTriggersAfterStart()
    {
        if ($this->driverIs('Mysql')) {
            $found = $this->TableSniffer->fetchQuery('SHOW TRIGGERS');
        } elseif ($this->driverIs('Postgres')) {
            $found = $this->TableSniffer->fetchQuery('SELECT tgname FROM pg_trigger');
        } elseif ($this->driverIs('Sqlite')) {
            if ($this->TableSniffer->isInTempMode()) {
                $found = $this->TableSniffer->fetchQuery('SELECT name FROM sqlite_temp_master WHERE type = "trigger"');
            } else {
                $found = $this->TableSniffer->fetchQuery('SELECT name FROM sqlite_master WHERE type = "trigger"');
            }
        }

        foreach ($this->allExpectedTriggers() as $trigger) {
            $this->assertSame(true, in_array($trigger, $found), "Trigger $trigger was not found");
        }
    }

    public function testGetAllTablesExceptPhinxlogs()
    {
        $found = $this->TableSniffer->getAllTablesExceptPhinxlogs(true);
        $expected = $this->getAllTables();
        if ($this->TableSniffer->isInMainMode()) {
            $expected[] = BaseTriggerBasedTableSniffer::DIRTY_TABLE_COLLECTOR;
        }

        $this->assertArraysHaveSameContent($expected, $found);
    }

    public function testGetAllTablesExceptPhinxlogsAndCollector()
    {
        $found = $this->TableSniffer->getAllTablesExceptPhinxlogsAndCollector(true);
        $this->assertArraysHaveSameContent($this->getAllTables(), $found);
    }

    public function testMarkAllTablesAsDirty()
    {
        $this->TableSniffer->markAllTablesAsDirty();
        $dirtyTables = $this->TableSniffer->getDirtyTables();
        $this->assertArraysHaveSameContent($this->getAllTables(), $dirtyTables);
    }

    public function testGetTriggers()
    {
        $this->assertArraysHaveSameContent($this->allExpectedTriggers(), $this->TableSniffer->getTriggers());
    }

    /**
     * Expect an exception since triggers already exist
     */
    public function testCreateTriggers()
    {
        $this->expectException(\PDOException::class);
        $this->TableSniffer->createTriggers();
    }

    public function testSwitchMode()
    {
        $mode = $this->TableSniffer->getMode();

        foreach ([1, 2, 3] as $i) {
            $this->TableSniffer->activateTempMode();
            $this->assertSame(false, in_array(BaseTriggerBasedTableSniffer::DIRTY_TABLE_COLLECTOR, $this->TableSniffer->getAllTables(true)));

            $this->TableSniffer->activateMainMode();
            $this->assertSame(true, in_array(BaseTriggerBasedTableSniffer::DIRTY_TABLE_COLLECTOR, $this->TableSniffer->getAllTables(true)));
        }

        $this->TableSniffer->setMode($mode);
    }

    public function testRecreateDirtyTableCollectorAfterDrop()
    {
        (new ConnectionHelper())->dropTables('test');
        (new Migrations())->migrate(['connection' => 'test']);

        $tables = $this->TableSniffer->getAllTablesExceptPhinxlogs(true);
        $this->assertFalse(in_array(BaseTriggerBasedTableSniffer::DIRTY_TABLE_COLLECTOR, $tables));

        $this->TableSniffer->init();

        $tables = $this->TableSniffer->getAllTablesExceptPhinxlogs(true);


        $exp = !getenv('SNIFFERS_IN_TEMP_MODE');
        $this->assertSame($exp, in_array(BaseTriggerBasedTableSniffer::DIRTY_TABLE_COLLECTOR, $tables));
    }
}
