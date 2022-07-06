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

use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\Runner;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Middleware\SocialAuthMiddleware;
use CakeDC\Auth\Social\Service\OAuth2Service;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Uri;

class SocialAuthMiddlewareTest extends TestCase
{
    public $fixtures = [
        'plugin.CakeDC/Auth.Users',
        'plugin.CakeDC/Auth.SocialAccounts',
    ];

    /**
     * @var \League\OAuth2\Client\Provider\Facebook
     */
    public $Provider;

    /**
     * @var \Cake\Http\ServerRequest
     */
    public $Request;

    /**
     * Setup the test case, backup the static object values so they can be restored.
     * Specifically backs up the contents of Configure and paths in App if they have
     * not already been backed up.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Provider = $this->getMockBuilder(\League\OAuth2\Client\Provider\Facebook::class)->setConstructorArgs([
            [
                'graphApiVersion' => 'v2.8',
                'redirectUri' => '/auth/facebook',
                'linkSocialUri' => '/link-social/facebook',
                'callbackLinkSocialUri' => '/callback-link-social/facebook',
                'clientId' => '10003030300303',
                'clientSecret' => 'secretpassword',
            ],
            [],
        ])->setMethods([
            'getAccessToken', 'getState', 'getAuthorizationUrl', 'getResourceOwner',
        ])->getMock();

        $config = [
            'service' => \CakeDC\Auth\Social\Service\OAuth2Service::class,
            'className' => $this->Provider,
            'mapper' => \CakeDC\Auth\Social\Mapper\Facebook::class,
            'options' => [
                'state' => '__TEST_STATE__',
                'graphApiVersion' => 'v2.8',
                'redirectUri' => '/auth/facebook',
                'linkSocialUri' => '/link-social/facebook',
                'callbackLinkSocialUri' => '/callback-link-social/facebook',
                'clientId' => '10003030300303',
                'clientSecret' => 'secretpassword',
            ],
            'collaborators' => [],
            'signature' => null,
            'mapFields' => [],
            'path' => [
                'plugin' => 'CakeDC/Auth',
                'controller' => 'Users',
                'action' => 'socialLogin',
                'prefix' => null,
            ],
        ];
        Configure::write('OAuth.providers.facebook', $config);

        $this->Request = ServerRequestFactory::fromGlobals();
    }

    /**
     * teardown any static object changes and restore them.
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->Provider, $this->Request);
    }

    /**
     * Test when user is on step one
     *
     * @return void
     */
    public function testProceedStepOne()
    {
        $uri = new Uri('/auth/facebook');
        $this->Request = $this->Request->withUri($uri);

        $this->Request = $this->Request->withAttribute('params', [
            'plugin' => 'CakeDC/Auth',
            'controller' => 'Users',
            'action' => 'socialLogin',
            'provider' => 'facebook',
        ]);

        $this->Provider->expects($this->any())
            ->method('getState')
            ->will($this->returnValue('_NEW_STATE_'));

        $this->Provider->expects($this->any())
            ->method('getAuthorizationUrl')
            ->will($this->returnValue('http://facebook.com/redirect/url'));

        $Middleware = new SocialAuthMiddleware([
            'urlChecker' => 'Authentication.Default',
            'loginUrl' => ['/auth/facebook'],
        ]);
        $handler = $this->getMockBuilder(Runner::class)
            ->setMethods(['handle'])
            ->getMock();
        $handler->expects($this->never())
            ->method('handle');

        $result = $Middleware->process($this->Request, $handler);
        $this->assertInstanceOf(Response::class, $result);

        $actual = $this->Request->getSession()->read('oauth2state');
        $expected = '_NEW_STATE_';
        $this->assertEquals($expected, $actual);

        $actual = $result->getHeaderLine('Location');
        $expected = 'http://facebook.com/redirect/url';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test when user is on get user step
     *
     * @return void
     */
    public function testSuccessfullyAuthenticated()
    {
        $uri = new Uri('/auth/facebook');
        $this->Request = $this->Request->withUri($uri);
        $this->Request = $this->Request->withQueryParams([
            'code' => 'ZPO9972j3092304230',
            'state' => '__TEST_STATE__',
        ]);
        $this->Request = $this->Request->withAttribute('params', [
            'plugin' => 'CakeDC/Auth',
            'controller' => 'Users',
            'action' => 'socialLogin',
            'provider' => 'facebook',
        ]);
        $this->Request->getSession()->write('oauth2state', '__TEST_STATE__');
        $Middleware = new SocialAuthMiddleware([
            'urlChecker' => 'Authentication.Default',
            'loginUrl' => ['/auth/facebook'],
        ]);

        $response = new Response();
        $response = $response->withStringBody(__METHOD__ . time());

        $handler = new class extends Runner {
            /**
             * @var \Cake\Http\ServerRequest
             */
            public $request;

            /**
             * @var \Cake\Http\Response
             */
            public $testResponse;
            /**
             * Handles a request and produces a response.
             *
             * May call other collaborating code to generate the response.
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->request = $request;

                return $this->testResponse;
            }
        };
        $handler->testResponse = $response;
        $result = $Middleware->process($this->Request, $handler);
        $this->assertSame($result, $handler->testResponse);

        /**
         * @var OAuth2Service $service
         */
        $service = $handler->request->getAttribute('socialService');
        $this->assertInstanceOf(OAuth2Service::class, $service);
        $this->assertEquals('facebook', $service->getProviderName());
        $this->assertTrue($service->isGetUserStep($handler->request));
    }

    /**
     * Test when action is not valid for social login
     *
     * @return void
     */
    public function testNotValidAction()
    {
        $Middleware = new SocialAuthMiddleware([
            'urlChecker' => 'Authentication.Default',
            'loginUrl' => ['/auth/facebook'],
        ]);
        $response = new Response();
        $response = $response->withStringBody(__METHOD__ . time());
        $handler = $this->getMockBuilder(Runner::class)
            ->setMethods(['handle'])
            ->getMock();
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($this->Request))
            ->willReturn($response);

        $result = $Middleware->process($this->Request, $handler);
        $this->assertSame($response, $result);
    }
}
