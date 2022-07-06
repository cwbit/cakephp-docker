<?php
declare(strict_types=1);

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2020 Juan Pablo Ramirez and Nicolas Masson
 * @link          https://webrider.de/
 * @since         2.3.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace CakephpFixtureFactories\Scenario;

interface FixtureScenarioInterface
{
    /**
     * Create your bunch of test fixtures in this method.
     *
     * @param mixed ...$args Arguments passed to the scenario.
     * @return mixed|void
     */
    public function load(...$args);
}
