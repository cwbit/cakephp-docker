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

namespace CakeDC\Auth\Auth\Test\TestCase\Auth\Auth;

use Cake\TestSuite\TestCase;
use CakeDC\Auth\Rbac\PermissionMatchResult;

class PermissionMatchResultTest extends TestCase
{
    /**
     * @var PermissionMatchResult
     */
    protected $permissionMatchResult;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->permissionMatchResult = new PermissionMatchResult();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->permissionMatchResult);
    }

    /**
     * test
     */
    public function testConstruct()
    {
        $permissionMatchResult = new PermissionMatchResult();
        $this->assertFalse($permissionMatchResult->isAllowed());
        $this->assertSame('', $permissionMatchResult->getReason());

        $permissionMatchResult = new PermissionMatchResult(true, 'bazinga');
        $this->assertTrue($permissionMatchResult->isAllowed());
        $this->assertSame('bazinga', $permissionMatchResult->getReason());
    }

    public function testSetGet()
    {
        $this->assertTrue($this->permissionMatchResult->setAllowed(true)->isAllowed());
        $this->assertFalse($this->permissionMatchResult->setAllowed(false)->isAllowed());
        $this->assertNotSame('bazinga', $this->permissionMatchResult->setReason('another-reason')->getReason());
        $this->assertSame('bazinga', $this->permissionMatchResult->setReason('bazinga')->getReason());
    }
}
