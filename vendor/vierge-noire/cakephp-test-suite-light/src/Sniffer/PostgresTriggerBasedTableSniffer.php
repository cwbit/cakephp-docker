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
use CakephpTestSuiteLight\Sniffer\DriverTraits\PostgresSnifferTrait;

class PostgresTriggerBasedTableSniffer extends BaseTriggerBasedTableSniffer
{
    use PostgresSnifferTrait;

    /**
     * @inheritDoc
     */
    public function truncateDirtyTables(): void
    {
        $truncate = function() {
            $this->getConnection()->transactional(function (Connection $connection) {
                $connection->execute('CALL TruncateDirtyTables();');
                $connection->execute('TRUNCATE TABLE ' . $this->collectorName() . ' RESTART IDENTITY CASCADE;');
            });
        };

        try {
            $truncate();
        } catch (\Throwable $e) {
//            // The dirty table collector might not be found because the session
//            // was interrupted.
            $this->init();
            try {
                $truncate();
            } catch (\Throwable $e) {
                throw new \RuntimeException($e->getMessage());
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function createTriggers(): void
    {
        $dirtyTable = self::DIRTY_TABLE_COLLECTOR;

        $stmts = [];
        foreach ($this->getAllTablesExceptPhinxlogsAndCollector(true) as $table) {
            $stmts[] = "
                CREATE OR REPLACE FUNCTION mark_table_{$table}_as_dirty() RETURNS TRIGGER LANGUAGE PLPGSQL AS $$
                DECLARE
                    spy_is_inactive {$dirtyTable}%ROWTYPE;
                BEGIN              
                    INSERT INTO {$dirtyTable} (table_name) VALUES ('{$table}') ON CONFLICT DO NOTHING;
                    RETURN NEW;
                END;
                $$
                ";

            $stmts[] = "                
                CREATE TRIGGER {$this->getTriggerName($table)} AFTER INSERT ON \"{$table}\"                
                FOR EACH ROW
                    EXECUTE PROCEDURE mark_table_{$table}_as_dirty();                                                                                                                    
                ";
        }
        foreach ($stmts as $stmt) {
            $this->getConnection()->execute($stmt);
        }
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
    public function createTruncateDirtyTablesProcedure(): void
    {
        $this->getConnection()->execute("
            CREATE OR REPLACE PROCEDURE TruncateDirtyTables() AS $$
            DECLARE
                _rec    record;
            BEGIN           
                FOR _rec IN (
                    SELECT  * FROM {$this->collectorName()} dt
                    INNER JOIN information_schema.tables info_schema on dt.table_name = info_schema.table_name                    
                    WHERE info_schema.table_schema = 'public'
                ) LOOP
                    BEGIN
                        EXECUTE 'TRUNCATE TABLE \"' || _rec.table_name || '\" RESTART IDENTITY CASCADE';
                    END;
                END LOOP;                                
                RETURN;                                
            END $$ LANGUAGE plpgsql;
        ");
    }

    /**
     * @inheritDoc
     */
    public function markAllTablesAsDirty(): void
    {
        $tables = $this->getAllTablesExceptPhinxlogsAndCollector();
        $this->getConnection()->execute(
            "INSERT INTO {$this->collectorName()} VALUES ('" . implode("'), ('", $tables) . "') ON CONFLICT DO NOTHING"
        );
    }
}
