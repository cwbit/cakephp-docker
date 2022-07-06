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
namespace CakeDC\Users\Test\TestCase\Authentication;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Authentication\DefaultWebauthn2fAuthenticationChecker;

/**
 * Test case for DefaultWebauthn2fAuthenticationChecker class
 *
 * @package CakeDC\Users\Test\TestCase\Auth
 */
class DefaultWebauthn2fAuthenticationCheckerTest extends TestCase
{
    /**
     * Test isEnabled method
     *
     * @return void
     */
    public function testIsEnabled()
    {
        Configure::write('Webauthn2fa.enabled', false);
        $Checker = new DefaultWebauthn2fAuthenticationChecker();
        $this->assertFalse($Checker->isEnabled());

        Configure::write('Webauthn2fa.enabled', true);
        $Checker = new DefaultWebauthn2fAuthenticationChecker();
        $this->assertTrue($Checker->isEnabled());

        Configure::delete('Webauthn2fa.enabled');
        $Checker = new DefaultWebauthn2fAuthenticationChecker();
        $this->assertTrue($Checker->isEnabled());
    }

    /**
     * Test isRequired method
     *
     * @return void
     */
    public function testIsRequired()
    {
        Configure::write('Webauthn2fa.enabled', false);
        $Checker = new DefaultWebauthn2fAuthenticationChecker();
        $this->assertFalse($Checker->isRequired(['id' => 10]));

        Configure::write('Webauthn2fa.enabled', true);
        $Checker = new DefaultWebauthn2fAuthenticationChecker();
        $this->assertTrue($Checker->isRequired(['id' => 10]));

        Configure::delete('Webauthn2fa.enabled');
        $Checker = new DefaultWebauthn2fAuthenticationChecker();
        $this->assertTrue($Checker->isRequired(['id' => 10]));

        $Checker = new DefaultWebauthn2fAuthenticationChecker();
        $this->assertFalse($Checker->isRequired([]));
    }
}
