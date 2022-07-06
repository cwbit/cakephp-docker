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

use Authentication\Authenticator\Result;
use Cake\Core\Configure;
use Cake\Http\Client\Response;
use Cake\Http\ServerRequestFactory;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Authentication\AuthenticationService;
use CakeDC\Auth\Authentication\Failure;
use CakeDC\Auth\Authenticator\FormAuthenticator;

class AuthenticationServiceTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.CakeDC/Auth.Users',
        'plugin.CakeDC/Auth.SocialAccounts',
    ];

    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticateEmptyAuthenticators()
    {
        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'user-not-found', 'password' => 'password']
        );
        $response = new Response();

        $service = new AuthenticationService([
            'identifiers' => [
                'Authentication.Password',
            ],
            'authenticators' => [],
        ]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No authenticators loaded. You need to load at least one authenticator.');
        $service->authenticate($request);
    }

    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticateFail()
    {
        $Table = TableRegistry::getTableLocator()->get('CakeDC/Auth.Users');
        $Table->setEntityClass(\CakeDC\Auth\Test\App\Model\Entity\User::class);
        $entity = $Table->get('00000000-0000-0000-0000-000000000001');
        $entity->password = 'password';
        $this->assertTrue((bool)$Table->save($entity));
        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'user-not-found', 'password' => 'password']
        );
        $response = new Response();

        $service = new AuthenticationService([
            'identifiers' => [
                'Authentication.Password',
            ],
            'authenticators' => [
                'Authentication.Session',
                'CakeDC/Auth.Form',
            ],
        ]);

        $result = $service->authenticate($request);
        $this->assertNull($result->getdata());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isValid());
        $provider = $service->getAuthenticationProvider();
        $this->assertNull($provider);

        $sessionFailure = new Failure(
            $service->authenticators()->get('Session'),
            new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND)
        );
        $formFailure = new Failure(
            $service->authenticators()->get('Form'),
            new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, [
                'Password' => [],
            ])
        );
        $expected = [$sessionFailure, $formFailure];
        $actual = $service->getFailures();
        $this->assertEquals($expected, $actual);
    }

    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticate()
    {
        Configure::write('OneTimePasswordAuthenticator.login', false);
        Configure::write('U2f.enabled', false);
        $Table = TableRegistry::getTableLocator()->get('CakeDC/Auth.Users');
        $Table->setEntityClass(\CakeDC\Auth\Test\App\Model\Entity\User::class);
        $entity = $Table->get('00000000-0000-0000-0000-000000000001');
        $entity->password = 'password';
        $this->assertTrue((bool)$Table->save($entity));
        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'user-1', 'password' => 'password']
        );

        $service = new AuthenticationService([
            'identifiers' => [
                'Authentication.Password',
            ],
            'authenticators' => [
                'Authentication.Session',
                'CakeDC/Auth.Form',
            ],
        ]);

        $result = $service->authenticate($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertEquals(
            'user-1',
            $result->getData()['username']
        );
        $provider = $service->getAuthenticationProvider();
        $this->assertInstanceOf(FormAuthenticator::class, $provider);

        $sessionFailure = new Failure(
            $service->authenticators()->get('Session'),
            new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND)
        );
        $expected = [$sessionFailure];
        $actual = $service->getFailures();
        $this->assertEquals($expected, $actual);
    }

    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticateShouldDoGoogleVerifyEnabled()
    {
        Configure::write('OneTimePasswordAuthenticator.login', true);
        Configure::write('U2f.enabled', false);
        $Table = TableRegistry::getTableLocator()->get('CakeDC/Auth.Users');
        $Table->setEntityClass(\CakeDC\Auth\Test\App\Model\Entity\User::class);
        $entity = $Table->get('00000000-0000-0000-0000-000000000001');
        $entity->password = 'password';
        $this->assertTrue((bool)$Table->save($entity));
        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'user-1', 'password' => 'password']
        );

        $service = new AuthenticationService([
            'identifiers' => [
                'Authentication.Password' => [],
            ],
            'authenticators' => [
                'Authentication.Session' => [
                    'skipTwoFactorVerify' => true,
                ],
                'CakeDC/Auth.Form' => [
                    'skipTwoFactorVerify' => false,
                ],
            ],
        ]);

        $result = $service->authenticate($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertEquals(AuthenticationService::NEED_TWO_FACTOR_VERIFY, $result->getStatus());
        $this->assertNull($result->getData());
        $this->assertEquals(
            'user-1',
            $request->getAttribute('session')->read('temporarySession.username')
        );
        $sessionFailure = new Failure(
            $service->authenticators()->get('Session'),
            new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND)
        );
        $expected = [$sessionFailure];
        $actual = $service->getFailures();
        $this->assertEquals($expected, $actual);
    }

    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticateShouldDoGoogleVerifyDisabled()
    {
        Configure::write('U2f.enabled', false);
        Configure::write('OneTimePasswordAuthenticator.login', false);
        $Table = TableRegistry::getTableLocator()->get('CakeDC/Auth.Users');
        $Table->setEntityClass(\CakeDC\Auth\Test\App\Model\Entity\User::class);
        $entity = $Table->get('00000000-0000-0000-0000-000000000001');
        $entity->password = 'password';
        $this->assertTrue((bool)$Table->save($entity));
        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'user-1', 'password' => 'password']
        );

        $service = new AuthenticationService([
            'identifiers' => [
                'Authentication.Password' => [],
            ],
            'authenticators' => [
                'Authentication.Session' => [
                    'skipTwoFactorVerify' => true,
                ],
                'CakeDC/Auth.Form' => [
                    'skipTwoFactorVerify' => false,
                ],
            ],
        ]);

        $result = $service->authenticate($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertEquals(
            'user-1',
            $result->getData()['username']
        );
        $result = $service->getAuthenticationProvider();
        $this->assertInstanceOf(FormAuthenticator::class, $result);

        $sessionFailure = new Failure(
            $service->authenticators()->get('Session'),
            new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND)
        );
        $expected = [$sessionFailure];
        $actual = $service->getFailures();
        $this->assertEquals($expected, $actual);
    }
    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticateShouldDoWebauthn2faEnabled()
    {
        Configure::write('Webauthn2fa.enabled', true);
        $Table = TableRegistry::getTableLocator()->get('CakeDC/Auth.Users');
        $Table->setEntityClass(\CakeDC\Auth\Test\App\Model\Entity\User::class);
        $entity = $Table->get('00000000-0000-0000-0000-000000000001');
        $entity->password = 'password';
        $this->assertTrue((bool)$Table->save($entity));
        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'user-1', 'password' => 'password']
        );
        $response = new Response();

        $service = new AuthenticationService([
            'identifiers' => [
                'Authentication.Password' => [],
            ],
            'authenticators' => [
                'Authentication.Session' => [
                    'skipTwoFactorVerify' => true,
                ],
                'CakeDC/Auth.Form' => [
                    'skipTwoFactorVerify' => false,
                ],
            ],
        ]);

        $result = $service->authenticate($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertEquals(AuthenticationService::NEED_WEBAUTHN_2FA_VERIFY, $result->getStatus());
        $this->assertNull($request->getAttribute('session')->read('Auth.username'));
        $this->assertEquals(
            'user-1',
            $request->getAttribute('session')->read('Webauthn2fa.User.username')
        );
        $sessionFailure = new Failure(
            $service->authenticators()->get('Session'),
            new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND)
        );
        $expected = [$sessionFailure];
        $actual = $service->getFailures();
        $this->assertEquals($expected, $actual);
    }

    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticateShouldDoU2fEnabled()
    {
        Configure::write('U2f.enabled', true);
        $Table = TableRegistry::getTableLocator()->get('CakeDC/Auth.Users');
        $Table->setEntityClass(\CakeDC\Auth\Test\App\Model\Entity\User::class);
        $entity = $Table->get('00000000-0000-0000-0000-000000000001');
        $entity->password = 'password';
        $this->assertTrue((bool)$Table->save($entity));
        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'user-1', 'password' => 'password']
        );
        $response = new Response();

        $service = new AuthenticationService([
            'identifiers' => [
                'Authentication.Password' => [],
            ],
            'authenticators' => [
                'Authentication.Session' => [
                    'skipTwoFactorVerify' => true,
                ],
                'CakeDC/Auth.Form' => [
                    'skipTwoFactorVerify' => false,
                ],
            ],
        ]);

        $result = $service->authenticate($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertEquals(AuthenticationService::NEED_U2F_VERIFY, $result->getStatus());
        $this->assertNull($request->getAttribute('session')->read('Auth.username'));
        $this->assertEquals(
            'user-1',
            $request->getAttribute('session')->read('U2f.User.username')
        );
        $sessionFailure = new Failure(
            $service->authenticators()->get('Session'),
            new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND)
        );
        $expected = [$sessionFailure];
        $actual = $service->getFailures();
        $this->assertEquals($expected, $actual);
    }

    /**
     * testAuthenticate
     *
     * @return void
     */
    public function testAuthenticateShouldDoU2fDisabled()
    {
        Configure::write('U2f.enabled', false);
        $Table = TableRegistry::getTableLocator()->get('CakeDC/Auth.Users');
        $Table->setEntityClass(\CakeDC\Auth\Test\App\Model\Entity\User::class);
        $entity = $Table->get('00000000-0000-0000-0000-000000000001');
        $entity->password = 'password';
        $this->assertTrue((bool)$Table->save($entity));
        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'user-1', 'password' => 'password']
        );
        $response = new Response();

        $service = new AuthenticationService([
            'identifiers' => [
                'Authentication.Password' => [],
            ],
            'authenticators' => [
                'Authentication.Session' => [
                    'skipTwoFactorVerify' => true,
                ],
                'CakeDC/Auth.Form' => [
                    'skipTwoFactorVerify' => false,
                ],
            ],
        ]);

        $result = $service->authenticate($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isValid());

        $this->assertEquals(
            'user-1',
            $result->getData()['username']
        );

        $provider = $service->getAuthenticationProvider();
        $this->assertInstanceOf(FormAuthenticator::class, $provider);

        $sessionFailure = new Failure(
            $service->authenticators()->get('Session'),
            new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND)
        );
        $expected = [$sessionFailure];
        $actual = $service->getFailures();
        $this->assertEquals($expected, $actual);
    }
}
