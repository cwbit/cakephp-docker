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

namespace CakephpTestSuiteLight\Test\TestCase;

use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use CakephpTestSuiteLight\Fixture\TruncateDirtyTables;
use CakephpTestSuiteLight\FixtureManager;
use CakephpTestSuiteLight\StatisticTool;
use TestApp\Test\Fixture\CitiesFixture;
use TestApp\Test\Fixture\CountriesFixture;

class StatisticToolTest extends TestCase
{
    use TruncateDirtyTables;

    /**
     * @var StatisticTool
     */
    public $StatisticTool;

    public $fixtures = [
        // The order here is important
        CountriesFixture::class,
        CitiesFixture::class,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->StatisticTool = new StatisticTool(
            new FixtureManager(),
            true
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->StatisticTool);
    }

    /**
     * Given 2 tables are created and the process time is 0.129s
     * When the fixture manager collects dirty tables
     * And the statistics get collected
     * Then the statistics should be coherent
     */
    public function testCollectTestStatistics()
    {
        // Arrange
        $this->StatisticTool->startsTestTime();
        $this->StatisticTool->startsLoadingFixturesTime();
        usleep(1000);
        $this->StatisticTool->stopsLoadingFixturesTime();
        $this->StatisticTool->stopsTestTime();

        $this->StatisticTool->collectTestStatistics($this);
        $db = ConnectionManager::get('test')->config()['database'];

        // Act
        $stats = $this->StatisticTool->getStatistics();

        // Assert
        $this->assertSame(1, count($stats));
        $stats = $stats[0];

        // Duration of the test
        $this->assertSame(true, $stats[0] > 0);
        // Test class name
        $this->assertSame(self::class, $stats[1]);
        // Test method name
        $this->assertSame(__FUNCTION__, $stats[2]);
        // Number of dirty tables
        $this->assertSame(2, $stats[3]);
        // List of dirty tables
        $this->assertSame("$db.cities, $db.countries", $stats[4]);
    }

    public function testWriteStatsInCsv()
    {
        $this->StatisticTool->writeStatsInCsv();

        $this->assertFileExists(TMP . 'test_suite_light' . DS . 'test_suite_statistics.csv');
    }
}
