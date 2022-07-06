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
use Cake\TestSuite\TestCase;
use CakephpTestSuiteLight\Fixture\TruncateDirtyTables;
use CakephpTestSuiteLight\Sniffer\BaseTriggerBasedTableSniffer;
use CakephpTestSuiteLight\Sniffer\SnifferRegistry;
use CakephpTestSuiteLight\Test\Traits\ArrayComparerTrait;
use CakephpTestSuiteLight\Test\Traits\ExpectedSchemaTestTrait;
use Migrations\Migrations;

class TableSnifferWithMigrationTest extends TestCase
{
    use ArrayComparerTrait;
    use ExpectedSchemaTestTrait;
    use TruncateDirtyTables;

    /**
     * @var Migrations
     */
    public $migrations;

    /**
     * @var BaseTriggerBasedTableSniffer
     */
    public $TableSniffer;

    /**
     * @var bool
     */
    public static $snifferWasInTempMod;

    public static function setUpBeforeClass(): void
    {
        if (SnifferRegistry::get('test')->isInTempMode()) {
            SnifferRegistry::get('test')->activateMainMode();
            self::$snifferWasInTempMod = true;
        }
    }

    public static function tearDownAfterClass(): void
    {
        // Drop the product entry
        ConnectionManager::get('test')
            ->newQuery()
            ->delete(BaseTriggerBasedTableSniffer::DIRTY_TABLE_COLLECTOR)
            ->where(['table_name' => 'products'])
            ->execute();

        if (self::$snifferWasInTempMod) {
            SnifferRegistry::get('test')->activateTempMode();
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->TableSniffer = SnifferRegistry::get('test');

        $config = [
            'connection' => 'test',
            'source' => 'TestMigrations',
        ];

        $this->migrations = new Migrations();
        $this->migrations->migrate($config);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->TableSniffer);

        $this->migrations->rollback([
            'connection' => 'test',
            'source' => 'TestMigrations',
        ]);
        $this->migrations->rollback([
            'connection' => 'test',
            'source' => 'TestMigrations',
        ]);
    }

    protected function countProducts(): int
    {
        return (int) $nProducts = $this->TableSniffer->fetchQuery(
            'SELECT COUNT(*) FROM products'
        )[0];
    }

    /**
     * Find dirty tables
     * Since the table products was created
     * after the setup of the sniffer triggers,
     * it is not marked as dirty
     */
    public function testPopulateWithMigrationsWithoutRestart()
    {
        $tables = $this->TableSniffer->fetchAllTables();
        $this->assertTrue(in_array('products', $tables));
        $this->assertSame([], $this->TableSniffer->getDirtyTables());;
    }

    public function testPopulateWithMigrationsWithRestart()
    {
        $tables = $this->TableSniffer->fetchAllTables();
        $this->assertTrue(in_array('products', $tables));

        // Rollback the table products population migration
        $this->migrations->rollback([
            'connection' => 'test',
            'source' => 'TestMigrations',
        ]);

        $expected = $this->allExpectedTriggers();

        $this->assertArraysHaveSameContent($expected, $this->TableSniffer->getTriggers());

        // Reset the triggers
        $this->TableSniffer->restart();

        $expected[] = 'dts_products';
        $this->assertArraysHaveSameContent($expected, $this->TableSniffer->getTriggers());

        $nProducts = $this->countProducts();

        // Populate the products table
        $this->migrations->migrate([
            'connection' => 'test',
            'source' => 'TestMigrations',
        ]);

        $this->assertArraysHaveSameContent($expected, $this->TableSniffer->getTriggers());

        // Assert that a product was created
        $this->assertSame($nProducts + 1, $this->countProducts());

        // Assert that the products table is marked dirty
        $this->assertContains('products', $this->TableSniffer->getDirtyTables());
    }
}
