<?php
declare(strict_types=1);

/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2018, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace CakeDC\Auth\Test\TestCase\Authentication;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Authentication\DefaultU2fAuthenticationChecker;
use CakeDC\Auth\Authentication\U2fAuthenticationCheckerFactory;

class U2fAuthenticationCheckerFactoryTest extends TestCase
{
    /**
     * Test getChecker method
     *
     * @return void
     */
    public function testGetChecker()
    {
        $result = (new U2fAuthenticationCheckerFactory())->build();
        $this->assertInstanceOf(DefaultU2fAuthenticationChecker::class, $result);
    }

    /**
     * Test getChecker method
     *
     * @return void
     */
    public function testGetCheckerInvalidInterface()
    {
        Configure::write('U2f.checker', \stdClass::class);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid config for 'U2f.checker', 'stdClass' does not implement 'CakeDC\Auth\Authentication\U2fAuthenticationCheckerInterface'");
        (new U2fAuthenticationCheckerFactory())->build();
    }
}
