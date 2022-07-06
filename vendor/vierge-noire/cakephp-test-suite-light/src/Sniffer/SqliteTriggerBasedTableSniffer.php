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


use Cake\Database\Connection;
use CakephpTestSuiteLight\Sniffer\DriverTraits\SqliteSnifferTrait;

class SqliteTriggerBasedTableSniffer extends BaseTriggerBasedTableSniffer
{
    use SqliteSnifferTrait;

    /**
     * @inheritDoc
     */
    public function collectorName(): string
    {
        return ($this->isInTempMode() ? 'temp.' : '') . parent::collectorName();
    }

    /**
     * @inheritDoc
     */
    public function truncateDirtyTables(): void
    {
        $tables = $this->getDirtyTables();

        // If a dirty table got dropped, it should be ignored
        $tables = array_intersect($tables, $this->getAllTables(true));

        if (empty($tables)) {
            return;
        }

        $this->getConnection()->disableConstraints(function (Connection $connection) use ($tables) {
            foreach ($tables as $table) {
                $connection->delete($table);
                try {
                    $connection->delete('sqlite_sequence', ['name' => $table]);
                } catch (\PDOException $e) {}
            }
        });

        $dirtyTable = $this->collectorName();
        try {
            $this->getConnection()->delete($dirtyTable); /** @phpstan-ignore-line */
        } catch (\Exception $e) {
            // The dirty table collector might not be found because the session
            // was interrupted.
            $this->init();
            $this->truncateDirtyTables();
        }
    }

    /**
     * @inheritDoc
     */
    public function createTriggers(): void
    {
        $dirtyTable = self::DIRTY_TABLE_COLLECTOR;
        $temporary = $this->isInTempMode() ? 'TEMPORARY' : '';

        $stmts = [];
        foreach ($this->getAllTablesExceptPhinxlogsAndCollector(true) as $table) {
            $stmts[] = "
            CREATE {$temporary} TRIGGER {$this->getTriggerName($table)} AFTER INSERT ON `$table` 
                BEGIN
                    INSERT OR IGNORE INTO {$dirtyTable} VALUES ('{$table}');
                END;
            ";
        }
        foreach ($stmts as $stmt) {
            $this->getConnection()->execute($stmt);
        }
    }

    /**
     * @inheritDoc
     */
    public function createTruncateDirtyTablesProcedure(): void
    {
        // Do nothing, as Sqlite does not support procedures
    }

    /**
     * @inheritDoc
     */
    public function shutdown(): void
    {
        parent::shutdown();

        $this->dropTriggers();
        $this->dropDirtyTableCollector();
    }

    /**
     * @inheritDoc
     */
    public function markAllTablesAsDirty(): void
    {
        $tables = $this->getAllTablesExceptPhinxlogsAndCollector();

        $stmt = "INSERT OR IGNORE INTO {$this->collectorName()} VALUES ('" . implode("'), ('", $tables) . "')";
        $this->getConnection()->execute($stmt);
    }
}
