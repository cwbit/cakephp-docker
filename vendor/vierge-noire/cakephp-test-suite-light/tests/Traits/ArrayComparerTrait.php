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

trait ArrayComparerTrait
{
    /**
     * @param array $expected
     * @param array $found
     * @return void
     */
    public function assertArraysHaveSameContent(array $expected, array $found)
    {
        sort($expected);
        sort($found);
        $this->assertSame($expected, $found);
    }
}
