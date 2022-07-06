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
namespace CakephpTestSuiteLight\Sniffer\DriverTraits;


use Cake\Database\Connection;
use CakephpTestSuiteLight\Sniffer\BaseTriggerBasedTableSniffer;

/**
 * Trait MysqlSnifferTrait
 * @package CakephpTestSuiteLight\Sniffer\DriverTraits
 */
trait MysqlSnifferTrait
{
    /**
     * @inheritDoc
     */
    public function getTriggers(): array
    {
        $triggers = $this->fetchQuery("SHOW triggers");

        foreach ($triggers as $k => $trigger) {
            if (strpos($trigger, BaseTriggerBasedTableSniffer::TRIGGER_PREFIX) !== 0) {
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

        $stmts = $this->implodeSpecial("DROP TRIGGER ", $triggers, ";");
        $this->getConnection()->execute($stmts);
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
            $connection->transactional(function(Connection $connection) use ($tables) {
                $connection->execute(
                    $this->implodeSpecial(
                        'DROP TABLE IF EXISTS `', $tables, '`;')
                );
            });
        });
    }
}
