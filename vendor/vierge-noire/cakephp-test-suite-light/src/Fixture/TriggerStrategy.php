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
namespace CakephpTestSuiteLight\Fixture;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\Fixture\FixtureHelper;
use Cake\TestSuite\Fixture\FixtureStrategyInterface;
use CakephpTestSuiteLight\Sniffer\SnifferRegistry;

/**
 * Fixture strategy that cleans up tables based on triggers detecting any
 * inserts in any tables.
 *
 * At the beginning of each test, the tables previously touched are cleaned.
 * At the end of the test, nothing is performed. It is therefore possible
 * to work with transactions, break them, and you may also query your test
 * database to ensure the output of a given test matches expectations.
 *
 */
class TriggerStrategy implements FixtureStrategyInterface
{
    /**
     * Configuration key to ignore the truncation of an array of connections
     */
    public const TEST_SUITE_LIGHT_IGNORED_CONNECTIONS_CONFIG_KEY = 'TestSuiteLightIgnoredConnections';

    /**
     * @inheritDoc
     */
    public function setupTest(array $fixtureNames): void
    {
        $this->truncateDirtyTables();
        $helper = new FixtureHelper();
        $fixtures = $helper->loadFixtures($fixtureNames);
        $helper->insert($fixtures);
    }

    /**
     * @inheritDoc
     */
    public function teardownTest(): void
    {
        // We do nothing here
    }

    /**
     * Scan all test connections and truncate the dirty tables
     * @return void
     */
    public function truncateDirtyTables(): void
    {
        foreach ($this->getActiveConnections() as $connection) {
            SnifferRegistry::get($connection)->truncateDirtyTables();
        }
    }

    /**
     * Cheks if a connection should be truncated or not.
     *
     * @param string $connectionName
     * @return bool
     */
    public function isConnectionTruncationSkipped(string $connectionName): bool
    {
        $ignoredConnections = Configure::read(self::TEST_SUITE_LIGHT_IGNORED_CONNECTIONS_CONFIG_KEY, []);

        // CakePHP 4 solves a DebugKit issue by creating an Sqlite connection
        // in tests/bootstrap.php. This connection should be ignored.
        if ($connectionName === 'test_debug_kit' || in_array($connectionName, $ignoredConnections)) {
            return true;
        }

        if ((ConnectionManager::getConfig($connectionName)['skipInTestSuiteLight'] ?? false) === true) {
            return true;
        }

        if ($connectionName === 'test' || strpos($connectionName, 'test_') === 0) {
            return false;
        }

        return true;
    }

    /**
     * Get all connections used by the manager
     * @return array
     */
    protected function getActiveConnections(): array
    {
        $connections = ConnectionManager::configured();
        foreach ($connections as $i => $connectionName) {
            if ($this->isConnectionTruncationSkipped($connectionName)) {
                unset($connections[$i]);
            }
        }

        return $connections;
    }
}
