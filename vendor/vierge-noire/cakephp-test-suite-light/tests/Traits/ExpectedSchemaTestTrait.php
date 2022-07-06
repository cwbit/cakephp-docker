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
namespace CakephpTestSuiteLight\Test\Traits;

trait ExpectedSchemaTestTrait
{
    /**
     * All the tables
     *
     * @return string[]
     */
    protected function getAllTables(): array
    {
        return [
            'cities',
            'countries',
            '123456789_123456789_123456789_123456789_123456789_123456789',
        ];
    }

    protected function getAllDirtyTables(): array
    {
        return [
            'cities',
            'countries',
        ];
    }

    /**
     * All the triggers
     *
     * @return string[]
     */
    private function allExpectedTriggers(): array
    {
        return [
            'dts_cities',
            'dts_countries',
            'dts_123456789_123456789_123456789_123456789_123456789_123456789',
        ];
    }
}
