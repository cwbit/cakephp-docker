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
namespace CakephpTestSuiteLight\Test\TestCase;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use TestApp\Model\Table\CountriesTable;

class FixtureInjectorSkipTruncationTest extends TestCase
{
    /**
     * @var CountriesTable
     */
    public $Countries;

    /**
     * @var int
     */
    public static $nInitialCities;

    /**
     * Get the original number of countries
     */
    public static function setUpBeforeClass(): void
    {
        self::$nInitialCities = TableRegistry::getTableLocator()->get('Countries')->find()->count();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->Countries = TableRegistry::getTableLocator()->get('Countries');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Countries);
    }

    public function iterator()
    {
        return [
            [1], [2], [3],
        ];
    }

    /**
     * @dataProvider iterator
     * @param int $expected
     */
    public function testTruncationSkipped(int $expected)
    {
        $country = $this->Countries->newEntity(['name' => 'foo']);
        $this->Countries->saveOrFail($country);
        $this->assertSame($expected + self::$nInitialCities, $this->Countries->find()->count());
    }
}
