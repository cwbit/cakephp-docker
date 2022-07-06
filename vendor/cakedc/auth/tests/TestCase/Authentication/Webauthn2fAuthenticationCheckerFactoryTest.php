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
use CakeDC\Auth\Authentication\DefaultWebauthn2fAuthenticationChecker;
use CakeDC\Auth\Authentication\Webauthn2fAuthenticationCheckerFactory;

class Webauthn2fAuthenticationCheckerFactoryTest extends TestCase
{
    /**
     * Test getChecker method
     *
     * @return void
     */
    public function testGetChecker()
    {
        $result = (new Webauthn2fAuthenticationCheckerFactory())->build();
        $this->assertInstanceOf(DefaultWebauthn2fAuthenticationChecker::class, $result);
    }

    /**
     * Test getChecker method
     *
     * @return void
     */
    public function testGetCheckerInvalidInterface()
    {
        Configure::write('Webauthn2fa.checker', \stdClass::class);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid config for 'Webauthn2fa.checker', 'stdClass' does not implement 'CakeDC\Auth\Authentication\Webauthn2FAuthenticationCheckerInterface'");
        (new Webauthn2fAuthenticationCheckerFactory())->build();
    }
}
