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

use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Response;
use Cake\Http\Runner;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Middleware\RbacMiddleware;
use CakeDC\Auth\Rbac\Rbac;

/**
 * Class RbacMiddlewareTest
 *
 * @package TestCase\Middleware
 */
class RbacMiddlewareTest extends TestCase
{
    /**
     * @var RbacMiddleware
     */
    protected $rbacMiddleware;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->rbacMiddleware = new RbacMiddleware();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->rbacMiddleware);
    }

    /**
     * @test
     */
    public function testInvokeForbidden()
    {
        $this->expectException(ForbiddenException::class);
        $request = new ServerRequest();
        $rbacMiddleware = $this->rbacMiddleware;
        $handler = $this->getMockBuilder(Runner::class)
            ->setMethods(['handle'])
            ->getMock();
        $handler->expects($this->never())
            ->method('handle');

        $rbacMiddleware->setConfig([
            'unauthorizedBehavior' => RbacMiddleware::UNAUTHORIZED_BEHAVIOR_THROW,
        ]);
        $rbacMiddleware->process($request, $handler);
    }

    /**
     * @test
     */
    public function testInvokeForbiddenAjax()
    {
        $this->expectException(ForbiddenException::class);
        $request = new ServerRequest();
        $request = $request->withHeader('Accept', 'application/json');
        $handler = $this->getMockBuilder(Runner::class)
            ->setMethods(['handle'])
            ->getMock();
        $handler->expects($this->never())
            ->method('handle');
        $rbacMiddleware = $this->rbacMiddleware;
        $rbacMiddleware->setConfig([
            'unauthorizedBehavior' => RbacMiddleware::UNAUTHORIZED_BEHAVIOR_AUTO,
        ]);
        $rbacMiddleware->process($request, $handler);
    }

    /**
     * @test
     */
    public function testInvokeRedirect()
    {
        $request = new ServerRequest();
        $rbacMiddleware = $this->rbacMiddleware;
        Router::reload();
        $builder = Router::createRouteBuilder('/');
        $builder->connect('/login', [
            'controller' => 'Users',
            'action' => 'login',
        ]);

        $handler = $this->getMockBuilder(Runner::class)
            ->setMethods(['handle'])
            ->getMock();
        $handler->expects($this->never())
            ->method('handle');

        $response = $rbacMiddleware->process($request, $handler);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/login', $response->getHeaderLine('Location'));
    }

    /**
     * @test
     */
    public function testInvokeAllowed()
    {
        $request = new ServerRequest();
        $userData = [
            'User' => [
                'id' => 1,
                'role' => 'user',
            ],
        ];
        $request = $request->withAttribute('identity', $userData);
        $response = new Response();
        $response = $response->withStringBody(__METHOD__ . time());
        $handler = $this->getMockBuilder(Runner::class)
            ->setMethods(['handle'])
            ->getMock();
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        $rbac = $this->getMockBuilder(Rbac::class)
            ->setMethods(['checkPermissions'])
            ->getMock();
        $rbac->expects($this->once())
            ->method('checkPermissions')
            ->with($userData['User'], $request)
            ->willReturn(true);
        $rbacMiddleware = new RbacMiddleware($rbac, [
            'unauthorizedBehavior' => RbacMiddleware::UNAUTHORIZED_BEHAVIOR_THROW,
        ]);
        $actual = $rbacMiddleware->process($request, $handler);
        $this->assertSame($response, $actual);
    }
}
