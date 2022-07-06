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

namespace CakeDC\Auth\Test\TestCase\Social\Service;

use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Social\Service\OAuth2Service;
use CakeDC\Auth\Social\Service\ServiceInterface;
use League\OAuth2\Client\Provider\FacebookUser;
use Zend\Diactoros\Uri;

class OAuth2ServiceTest extends TestCase
{
    /**
     * @var \CakeDC\Auth\Social\Service\OAuth2Service
     */
    public $Service;

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
            'authParams' => ['scope' => ['public_profile', 'email', 'user_birthday', 'user_gender', 'user_link']],
            'options' => [
                'state' => '__TEST_STATE__',
            ],
            'collaborators' => [],
            'signature' => null,
            'mapFields' => [],
            'path' => [
                'plugin' => 'CakeDC/Users',
                'controller' => 'Users',
                'action' => 'socialLogin',
                'prefix' => null,
            ],
        ];

        $this->Service = new OAuth2Service($config);

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

        unset($this->Provider, $this->Service, $this->Request);
    }

    /**
     * Test construct
     *
     * @return void
     */
    public function testConstruct()
    {
        $service = new OAuth2Service([
            'className' => \League\OAuth2\Client\Provider\Facebook::class,
            'mapper' => \CakeDC\Auth\Social\Mapper\Facebook::class,
            'authParams' => ['scope' => ['public_profile', 'email', 'user_birthday', 'user_gender', 'user_link']],
            'options' => [
                'customOption' => 'hello',
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
                'plugin' => 'CakeDC/Users',
                'controller' => 'Users',
                'action' => 'socialLogin',
                'prefix' => null,
            ],
        ]);
        $this->assertInstanceOf(ServiceInterface::class, $service);
    }

    /**
     * Test isGetUserStep, should return true
     *
     * @return void
     */
    public function testIsGetUserStep()
    {
        $uri = new Uri('/login');

        $sessionConfig = (array)Configure::read('Session') + [
                'defaults' => 'php',
            ];
        $session = Session::create($sessionConfig);
        $this->Request = new ServerRequest([
            'uri' => $uri,
            'session' => $session,
        ]);
        $this->Request = $this->Request->withQueryParams([
            'code' => 'ZPO9972j3092304230',
        ]);

        $result = $this->Service->isGetUserStep($this->Request);
        $this->assertTrue($result);
    }

    /**
     * Test isGetUserStep, when values is empty
     *
     * @return void
     */
    public function testIsGetUserStepWhenEmpty()
    {
        $uri = new Uri('/login');

        $sessionConfig = (array)Configure::read('Session') + [
                'defaults' => 'php',
            ];
        $session = Session::create($sessionConfig);
        $this->Request = new ServerRequest([
            'uri' => $uri,
            'session' => $session,
        ]);
        $this->Request = $this->Request->withQueryParams([
            'code' => '',
        ]);

        $result = $this->Service->isGetUserStep($this->Request);
        $this->assertFalse($result);
    }

    /**
     * Test isGetUserStep, when values is not provided
     *
     * @return void
     */
    public function testIsGetUserStepWhenNotProvided()
    {
        $uri = new Uri('/login');

        $sessionConfig = (array)Configure::read('Session') + [
                'defaults' => 'php',
            ];
        $session = Session::create($sessionConfig);
        $this->Request = new ServerRequest([
            'uri' => $uri,
            'session' => $session,
        ]);

        $result = $this->Service->isGetUserStep($this->Request);
        $this->assertFalse($result);
    }

    /**
     * Test getAuthorizationUrl method
     *
     * @return void
     */
    public function testGetAuthorizationUrl()
    {
        $this->Provider->expects($this->once())
            ->method('getState')
            ->will($this->returnValue('_NEW_STATE_'));

        $this->Provider->expects($this->once())
            ->method('getAuthorizationUrl')
            ->with($this->equalTo([
                'scope' => ['public_profile', 'email', 'user_birthday', 'user_gender', 'user_link'],
            ]))
            ->will($this->returnValue('http://facebook.com/redirect/url'));

        $actual = $this->Service->getAuthorizationUrl($this->Request);
        $expected = 'http://facebook.com/redirect/url';
        $this->assertEquals($expected, $actual);

        $actual = $this->Request->getSession()->read('oauth2state');
        $expected = '_NEW_STATE_';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test getUser method
     *
     * @return void
     */
    public function testGetUser()
    {
        $this->Request = $this->Request->withQueryParams([
            'code' => 'ZPO9972j3092304230',
            'state' => '__TEST_STATE__',
        ]);
        $this->Request->getSession()->write('oauth2state', '__TEST_STATE__');

        $Token = new \League\OAuth2\Client\Token\AccessToken([
            'access_token' => 'test-token',
            'expires' => 1490988496,
        ]);

        $user = new FacebookUser([
            'id' => '1',
            'name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@gmail.com',
            'hometown' => [
                'id' => '108226049197930',
                'name' => 'Madrid',
            ],
            'picture' => [
                'data' => [
                    'url' => 'https://scontent.xx.fbcdn.net/v/test.jpg',
                    'is_silhouette' => false,
                ],
            ],
            'cover' => [
                'source' => 'https://scontent.xx.fbcdn.net/v/test.jpg',
                'id' => '1',
            ],
            'gender' => 'male',
            'locale' => 'en_US',
            'link' => 'https://www.facebook.com/app_scoped_user_id/1/',
            'timezone' => -5,
            'age_range' => [
                'min' => 21,
            ],
            'bio' => 'I am the best test user in the world.',
            'picture_url' => 'https://scontent.xx.fbcdn.net/v/test.jpg',
            'is_silhouette' => false,
            'cover_photo_url' => 'https://scontent.xx.fbcdn.net/v/test.jpg',
        ]);

        $this->Provider->expects($this->never())
            ->method('getAuthorizationUrl');

        $this->Provider->expects($this->never())
            ->method('getState');

        $this->Provider->expects($this->once())
            ->method('getAccessToken')
            ->with(
                $this->equalTo('authorization_code'),
                $this->equalTo(['code' => 'ZPO9972j3092304230'])
            )
            ->will($this->returnValue($Token));

        $this->Provider->expects($this->once())
            ->method('getResourceOwner')
            ->with(
                $this->equalTo($Token)
            )
            ->will($this->returnValue($user));

        $actual = $this->Service->getUser($this->Request);

        $expected = [
            'token' => $Token,
            'id' => '1',
            'name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@gmail.com',
            'hometown' => [
                'id' => '108226049197930',
                'name' => 'Madrid',
            ],
            'picture' => [
                'data' => [
                    'url' => 'https://scontent.xx.fbcdn.net/v/test.jpg',
                    'is_silhouette' => false,
                ],
            ],
            'cover' => [
                'source' => 'https://scontent.xx.fbcdn.net/v/test.jpg',
                'id' => '1',
            ],
            'gender' => 'male',
            'locale' => 'en_US',
            'link' => 'https://www.facebook.com/app_scoped_user_id/1/',
            'timezone' => -5,
            'age_range' => [
                'min' => 21,
            ],
            'bio' => 'I am the best test user in the world.',
            'picture_url' => 'https://scontent.xx.fbcdn.net/v/test.jpg',
            'is_silhouette' => false,
            'cover_photo_url' => 'https://scontent.xx.fbcdn.net/v/test.jpg',
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test getUser method, state not equal
     *
     * @return void
     */
    public function testGetUserStateNotEqual()
    {
        $this->Request = $this->Request->withQueryParams([
            'code' => 'ZPO9972j3092304230',
            'state' => '__Unknown_State__',
        ]);
        $this->Request->getSession()->write('oauth2state', '__TEST_STATE__');

        $this->Provider->expects($this->never())
            ->method('getAuthorizationUrl');

        $this->Provider->expects($this->never())
            ->method('getState');

        $this->Provider->expects($this->never())
            ->method('getAccessToken');

        $this->Provider->expects($this->never())
            ->method('getResourceOwner');

        $this->expectException(BadRequestException::class);
        $this->Service->getUser($this->Request);
    }

    /**
     * Test getUser method without code
     *
     * @return void
     */
    public function testGetUserWithoutCode()
    {
        $this->Request = $this->Request->withQueryParams([
            'state' => '__TEST_STATE__',
        ]);
        $this->Request->getSession()->write('oauth2state', '__TEST_STATE__');

        $this->Provider->expects($this->never())
            ->method('getAuthorizationUrl');

        $this->Provider->expects($this->never())
            ->method('getState');

        $this->Provider->expects($this->never())
            ->method('getAccessToken');

        $this->Provider->expects($this->never())
            ->method('getResourceOwner');

        $this->expectException(BadRequestException::class);
        $this->Service->getUser($this->Request);
    }
}
