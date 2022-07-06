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

namespace CakephpTestSuiteLight\Sniffer;


use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;

class SnifferRegistry
{
    const LOGGER = 'logger';

    /**
     * @var BaseTableSniffer[]
     */
    private static $sniffers = [];

    /**
     * @param string $connectionName
     * @return BaseTableSniffer
     */
    public static function set(string $connectionName): BaseTableSniffer
    {
        $snifferName = self::getConnectionSnifferName($connectionName);
        return self::$sniffers[$connectionName] = new $snifferName(self::getConnection($connectionName));
    }

    /**
     * @param string $connectionName
     * @return BaseTableSniffer
     */
    public static function get(string $connectionName): BaseTableSniffer
    {
        if (!isset(self::$sniffers[$connectionName])) {
            self::set($connectionName);
        }

        return self::$sniffers[$connectionName];
    }

    /**
     * @param string $connectionName
     * @return bool
     */
    public static function exists(string $connectionName): bool
    {
        return isset(self::$sniffers[$connectionName]);
    }

    /**
     * Shutdown and clear all sniffers
     * @return void
     */
    public static function clear(): void
    {
        foreach (self::$sniffers as $conn => $sniffer) {
            $sniffer->shutdown();
            unset(self::$sniffers[$conn]);
        }
    }

    /**
     * @param string $name
     * @return ConnectionInterface
     */
    public static function getConnection($name = 'test'): ConnectionInterface
    {
        return ConnectionManager::get($name);
    }

    /**
     * Read in the config the sniffer to use
     * @param string $connectionName
     * @return string
     */
    public static function getConnectionSnifferName(string $connectionName): string
    {
        $config = ConnectionManager::getConfig($connectionName);
        $driver = '';

        if (isset($config['tableSniffer'])) {
            $snifferName = $config['tableSniffer'];
        } else {
            try {
                $driver = self::getConnection($connectionName)->config()['driver'];
                $snifferName = self::getDefaultTableSniffers()[$driver] ?? null;
                if (is_null($snifferName)) {
                    throw new \RuntimeException();
                }
            } catch (\RuntimeException $e) {
                $msg = "Testsuite light error for connection {$connectionName}. ";
                $msg .= "The DB driver {$driver} is not supported or was not found";
                throw new \PHPUnit\Framework\Exception($msg);
            }
        }
        return $snifferName;
    }

    /**
     * Table sniffers provided by the package
     * @return array
     */
    public static function getDefaultTableSniffers(): array
    {
        return [
            \Cake\Database\Driver\Mysql::class => MysqlTriggerBasedTableSniffer::class,
            \Cake\Database\Driver\Sqlite::class => SqliteTriggerBasedTableSniffer::class,
            \Cake\Database\Driver\Postgres::class => PostgresTriggerBasedTableSniffer::class,
        ];
    }
}