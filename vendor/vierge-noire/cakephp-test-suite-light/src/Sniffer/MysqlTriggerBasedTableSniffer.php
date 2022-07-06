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


use CakephpTestSuiteLight\Sniffer\DriverTraits\MysqlSnifferTrait;

class MysqlTriggerBasedTableSniffer extends BaseTriggerBasedTableSniffer
{
    use MysqlSnifferTrait;

    /**
     * @inheritDoc
     */
    public function truncateDirtyTables(): void
    {
        $truncate = function() {
            $this->getConnection()->execute('CALL TruncateDirtyTables();');
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
        $stmts = "";
        foreach ($this->getAllTablesExceptPhinxlogsAndCollector(true) as $table) {
            $stmts .= "       
            CREATE TRIGGER {$this->getTriggerName($table)} AFTER INSERT ON `{$table}`
            FOR EACH ROW                
                INSERT IGNORE INTO {$this->collectorName()} VALUES ('{$table}');                
            ";
        }

        if ($stmts !== '') {
            $this->getConnection()->execute($stmts);
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
            DROP PROCEDURE IF EXISTS TruncateDirtyTables;
            CREATE PROCEDURE TruncateDirtyTables()
            BEGIN
                DECLARE current_table_name VARCHAR(128);
                DECLARE finished INTEGER DEFAULT 0;
                DECLARE dirty_table_cursor CURSOR FOR
                    SELECT dt.table_name FROM (
                        SELECT * FROM {$this->collectorName()}
                        UNION
                        SELECT '{$this->collectorName()}'
                    ) dt
                    INNER JOIN INFORMATION_SCHEMA.TABLES info
                    ON info.table_name = dt.table_name;                    
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;
            
                SET FOREIGN_KEY_CHECKS=0;
                OPEN dirty_table_cursor;
                truncate_tables: LOOP
                    FETCH dirty_table_cursor INTO current_table_name;
                    IF finished = 1 THEN
                        LEAVE truncate_tables;
                    END IF;
                    SET @create_trigger_statement = CONCAT('TRUNCATE TABLE `', current_table_name, '`;');
                    PREPARE stmt FROM @create_trigger_statement;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                END LOOP truncate_tables;
                CLOSE dirty_table_cursor;
                            
                SET FOREIGN_KEY_CHECKS=1;
            END
        ");
    }

    /**
     * @inheritDoc
     */
    public function markAllTablesAsDirty(): void
    {
        $tables = $this->getAllTablesExceptPhinxlogsAndCollector();
        $this->getConnection()->execute(
            "INSERT IGNORE INTO {$this->collectorName()} VALUES ('" . implode("'), ('", $tables) . "')"
        );
    }
}
