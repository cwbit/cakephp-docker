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

namespace CakeDC\Auth\Test\TestCase\Middleware;

use Authentication\Authenticator\Result;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\Runner;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Authentication\AuthenticationService;
use CakeDC\Auth\Middleware\TwoFactorMiddleware;

/**
 * Class OneTimePasswordAuthenticatorMiddlewareTest
 *
 * @package TestCase\Middleware
 */
class TwoFactorMiddlewareTest extends TestCase
{
    /**
     * @var TwoFactorMiddleware
     */
    protected $TwoFactorMiddleware;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->TwoFactorMiddleware = new TwoFactorMiddleware();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->TwoFactorMiddleware);
    }

    /**
     * @test middleware when dont't need to processed to one time password verify
     */
    public function testInvokeNotNeeded()
    {
        $request = new ServerRequest();
        $response = new Response();
        $response = $response->withStringBody(__METHOD__ . time());
        $handler = $this->getMockBuilder(Runner::class)
            ->setMethods(['handle'])
            ->getMock();
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        $service = $this->getMockBuilder(AuthenticationService::class)->setConstructorArgs([
            [
                'identifiers' => [
                    'Authentication.Password',
                ],
                'authenticators' => [
                    'Authentication.Session',
                    'CakeDC/Auth.Form',
                ],
            ],
        ])->setMethods(['getResult'])->getMock();
        $result = new Result(['id' => 10, 'username' => 'johndoe'], Result::SUCCESS);
        $service->expects($this->any())
            ->method('getResult')
            ->will($this->returnValue($result));

        $request = $request->withAttribute('authentication', $service);
        $middleware = $this->TwoFactorMiddleware;
        $actual = $middleware->process($request, $handler);
        $this->assertSame($response, $actual);
    }

    /**
     * @test middleware when dont't need to processed to one time password verify
     */
    public function testInvokeNeedVerify()
    {
        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/login'],
            [],
            ['username' => 'user-1', 'password' => 'password', 'remember_me' => 1]
        );
        $handler = $this->getMockBuilder(Runner::class)
            ->setMethods(['handle'])
            ->getMock();
        $handler->expects($this->never())
            ->method('handle');
        Configure::write('OneTimePasswordAuthenticator.verifyAction', [
            'controller' => 'Users',
            'action' => 'verify',
        ]);
        Router::reload();
        $builder = Router::createRouteBuilder('/');
        $builder->connect('/verify', [
            'controller' => 'Users',
            'action' => 'verify',
        ]);
        $service = $this->getMockBuilder(AuthenticationService::class)->setConstructorArgs([
            [
                'identifiers' => [
                    'Authentication.Password',
                ],
                'authenticators' => [
                    'Authentication.Session',
                    'CakeDC/Auth.Form',
                ],
            ],
        ])->setMethods(['getResult'])->getMock();
        $result = new Result(null, AuthenticationService::NEED_TWO_FACTOR_VERIFY);
        $service->expects($this->any())
            ->method('getResult')
            ->will($this->returnValue($result));

        $request = $request->withAttribute('authentication', $service);
        $middleware = $this->TwoFactorMiddleware;
        $actual = $middleware->process($request, $handler);
        $this->assertInstanceOf(Response::class, $actual);
        $expected = [
            '/verify',
        ];
        $this->assertEquals($expected, $actual->getHeader('Location'));
        $this->assertSame(1, $request->getSession()->read('CookieAuth.remember_me'));
    }
}
