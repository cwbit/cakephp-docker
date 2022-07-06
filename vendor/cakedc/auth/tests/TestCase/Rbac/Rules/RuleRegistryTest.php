<?php
declare(strict_types=1);

/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Auth\Test\TestCase\Rbac\Rules;

use Cake\TestSuite\TestCase;
use CakeDC\Auth\Rbac\Rules\Owner;
use CakeDC\Auth\Rbac\Rules\RuleRegistry;

/**
 * @property Owner Owner
 * @property ServerRequest request
 */
class RuleRegistryTest extends TestCase
{
    /**
     * @return void
     */
    public function testGet()
    {
        RuleRegistry::clear();
        $ownerRule = RuleRegistry::get(Owner::class, ['key' => 'value']);
        $ownerSameRule = RuleRegistry::get(Owner::class, ['key' => 'value']);
        $ownerRule2 = RuleRegistry::get(Owner::class, ['key' => 'another']);

        $this->assertSame($ownerRule, $ownerSameRule);
        $this->assertSame('value', $ownerRule->getConfig('key'));
        $this->assertNotSame($ownerRule, $ownerRule2);
        $this->assertSame('another', $ownerRule2->getConfig('key'));
        $this->assertCount(2, RuleRegistry::toArray());
    }

    /**
     * @return void
     */
    public function testClear()
    {
        RuleRegistry::clear();
        $this->assertEmpty(RuleRegistry::toArray());

        RuleRegistry::get(Owner::class, ['key' => 'value']);
        $this->assertCount(1, RuleRegistry::toArray());

        RuleRegistry::clear();
        $this->assertEmpty(RuleRegistry::toArray());
    }
}
