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
 */namespace CakephpTestSuiteLight\Sniffer\DriverTraits;


use Cake\Database\Connection;
use CakephpTestSuiteLight\Sniffer\BaseTriggerBasedTableSniffer;

/**
 * Trait SqliteSnifferTrait
 * @package CakephpTestSuiteLight\Sniffer\DriverTraits
 */
trait SqliteSnifferTrait
{
    /**
     * @inheritDoc
     */
    public function getTriggers(): array
    {
        $triggerPrefix = BaseTriggerBasedTableSniffer::TRIGGER_PREFIX;

        $triggers = $this->fetchQuery("
            SELECT name FROM sqlite_master WHERE type = 'trigger' AND name LIKE '{$triggerPrefix}%'
            UNION
            SELECT name FROM sqlite_temp_master WHERE type = 'trigger' AND name LIKE '{$triggerPrefix}%'
        ");

        foreach ($triggers as $k => $trigger) {
            if (strpos($trigger, $triggerPrefix) !== 0) {
                unset($triggers[$k]);
            }
        }

        return (array)$triggers;
    }

    /**
     * @inheritDoc
     */
    public function dropTriggers(): void
    {
        $triggers = $this->getTriggers();

        if (empty($triggers)) {
            return;
        }

        foreach ($triggers as $trigger) {
            $this->getConnection()->execute("DROP TRIGGER {$trigger};");
        }
    }

    /**
     * @inheritDoc
     */
    public function dropTables(array $tables): void
    {
        if (empty($tables)) {
            return;
        }

        $this->getConnection()->disableConstraints(function (Connection $connection) use ($tables) {
            $tables[] = BaseTriggerBasedTableSniffer::DIRTY_TABLE_COLLECTOR;
            $connection->transactional(function(Connection $connection) use ($tables) {
                foreach ($tables as $table) {
                    $connection->execute("DROP TABLE IF EXISTS $table;");
                }
            });
        });
    }
}
