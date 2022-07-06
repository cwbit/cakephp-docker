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


use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use CakephpTestSuiteLight\Fixture\TruncateDirtyTables;
use CakephpTestSuiteLight\Sniffer\BaseTriggerBasedTableSniffer;
use CakephpTestSuiteLight\Sniffer\MysqlTriggerBasedTableSniffer;
use CakephpTestSuiteLight\Sniffer\PostgresTriggerBasedTableSniffer;
use CakephpTestSuiteLight\Sniffer\SnifferRegistry;
use CakephpTestSuiteLight\Sniffer\SqliteTriggerBasedTableSniffer;
use PHPUnit\Framework\Exception;

class SnifferRegistryTest extends TestCase
{
    use TruncateDirtyTables;

    public function dataProviderTestLoadDefaultSniffer()
    {
        return [
            [Mysql::class, MysqlTriggerBasedTableSniffer::class],
            [Sqlite::class, SqliteTriggerBasedTableSniffer::class],
            [Postgres::class, PostgresTriggerBasedTableSniffer::class],
        ];
    }

    /**
     * @param $driver
     * @param $sniffer
     * @dataProvider dataProviderTestLoadDefaultSniffer
     */
    public function testGetDefaultTableSniffers($driver, $sniffer)
    {
        $act = SnifferRegistry::getDefaultTableSniffers()[$driver];
        $this->assertEquals($sniffer, $act);
    }

    public function testGetConnectionSnifferNameOnNonExistingConnection()
    {
        $this->expectException(Exception::class);
        SnifferRegistry::getConnectionSnifferName('dummy');
    }

    public function testGetConnectionSnifferNameOnConnection()
    {
        $sniffer = 'FooSniffer';
        $connectionName = 'testGetConnectionSnifferNameOnConnection';
        $testConfig = ConnectionManager::getConfig('test');
        $testConfig['tableSniffer'] = $sniffer;
        ConnectionManager::setConfig($connectionName, $testConfig);
        $act = SnifferRegistry::getConnectionSnifferName($connectionName);
        $this->assertSame($sniffer, $act);
        ConnectionManager::drop($connectionName);
    }

    public function testModeIsCorrect()
    {
        $tables = SnifferRegistry::get('test')->fetchAllTables();
        $collectorIsVisible = in_array(BaseTriggerBasedTableSniffer::DIRTY_TABLE_COLLECTOR, $tables);
        if (getenv('SNIFFERS_IN_TEMP_MODE')) {
            $expected = false;
        } else {
            $expected = true;
        }
        $this->assertSame($expected, $collectorIsVisible);
    }
}
