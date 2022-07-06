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

use Cake\TestSuite\Fixture\FixtureStrategyInterface;

/**
 * Will truncate the test DBs before each test
 * This enables the developer to query her/his test database
 * after a test has run.
 *
 * Trait TruncateTablesTrait
 */
trait TruncateDirtyTables
{
    protected function getFixtureStrategy(): FixtureStrategyInterface
    {
        return new TriggerStrategy();
    }
}
