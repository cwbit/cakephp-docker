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
namespace CakeDC\Auth\Test\TestCase\Authenticator;

use Authentication\Authenticator\Result;
use Authentication\Identifier\IdentifierCollection;
use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Authenticator\TwoFactorAuthenticator;

class TwoFactorAuthenticatorTest extends TestCase
{
    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticateFailedNoData()
    {
        $uri = new \Zend\Diactoros\Uri('/testpath');
        $uri->base = null;
        $request = new \Cake\Http\ServerRequest();
        $request = $request->withUri($uri);
        $identifiers = new IdentifierCollection([
            'Authentication.Password',
        ]);

        $Authenticator = new TwoFactorAuthenticator($identifiers, [
            'loginUrl' => '/testpath',
        ]);

        $result = $Authenticator->authenticate($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(Result::FAILURE_CREDENTIALS_MISSING, $result->getStatus());
    }

    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticateFailedInvalidUrl()
    {
        $uri = new \Zend\Diactoros\Uri('/testpath');
        $uri->base = null;
        $request = new \Cake\Http\ServerRequest();
        $request = $request->withUri($uri);
        $request->getSession()->write(
            TwoFactorAuthenticator::USER_SESSION_KEY,
            new Entity([
                'id' => '42',
                'username' => 'marcelo',
                'role' => 'user',
            ])
        );
        $identifiers = new IdentifierCollection([
            'Authentication.Password',
        ]);
        $Authenticator = new TwoFactorAuthenticator($identifiers, [
            'loginUrl' => '/testpathnotsame',
        ]);

        $result = $Authenticator->authenticate($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(Result::FAILURE_OTHER, $result->getStatus());
    }

    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticate()
    {
        $uri = new \Zend\Diactoros\Uri('/testpath');
        $uri->base = null;
        $request = new \Cake\Http\ServerRequest();
        $request = $request->withUri($uri);
        $request->getSession()->write(
            TwoFactorAuthenticator::USER_SESSION_KEY,
            new Entity([
                'id' => '42',
                'username' => 'marcelo',
                'role' => 'user',
            ])
        );
        $identifiers = new IdentifierCollection([
            'Authentication.Password',
        ]);
        $Authenticator = new TwoFactorAuthenticator($identifiers, [
            'loginUrl' => '/testpath',
        ]);

        $result = $Authenticator->authenticate($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(Result::SUCCESS, $result->getStatus());
    }
}
