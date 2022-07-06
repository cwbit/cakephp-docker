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
namespace CakeDC\Auth\Test\TestCase\Authentication;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Authentication\DefaultOneTimePasswordAuthenticationChecker;

/**
 * Test case for DefaultTwoFactorAuthenticationChecker class
 *
 * @package CakeDC\Auth\Test\TestCase\Auth
 */
class DefaultOneTimePasswordAuthenticationCheckerTest extends TestCase
{
    /**
     * Test isEnabled method
     *
     * @return void
     */
    public function testIsEnabled()
    {
        Configure::write('OneTimePasswordAuthenticator.login', false);
        $Checker = new DefaultOneTimePasswordAuthenticationChecker('OneTimePasswordAuthenticator.login');
        $this->assertFalse($Checker->isEnabled());

        Configure::write('Users.OneTimePasswordAuthenticator.login', true);
        $Checker = new DefaultOneTimePasswordAuthenticationChecker('Users.OneTimePasswordAuthenticator.login');
        $this->assertTrue($Checker->isEnabled());

        Configure::write('OneTimePasswordAuthenticator.login', true);
        $Checker = new DefaultOneTimePasswordAuthenticationChecker('OneTimePasswordAuthenticator.login');
        $this->assertTrue($Checker->isEnabled());

        Configure::delete('OneTimePasswordAuthenticator.login');
        $Checker = new DefaultOneTimePasswordAuthenticationChecker('OneTimePasswordAuthenticator.login');
        $this->assertTrue($Checker->isEnabled());
    }

    /**
     * Test isRequired method
     *
     * @return void
     */
    public function testIsRequired()
    {
        Configure::write('OneTimePasswordAuthenticator.login', false);
        $Checker = new DefaultOneTimePasswordAuthenticationChecker();
        $this->assertFalse($Checker->isRequired(['id' => 10]));

        Configure::write('OneTimePasswordAuthenticator.login', true);
        $Checker = new DefaultOneTimePasswordAuthenticationChecker();
        $this->assertTrue($Checker->isRequired(['id' => 10]));

        Configure::delete('OneTimePasswordAuthenticator.login');
        $Checker = new DefaultOneTimePasswordAuthenticationChecker();
        $this->assertTrue($Checker->isRequired(['id' => 10]));

        $Checker = new DefaultOneTimePasswordAuthenticationChecker();
        $this->assertFalse($Checker->isRequired([]));
    }
}
