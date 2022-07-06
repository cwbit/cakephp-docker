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

namespace CakeDC\Auth\Test\TestCase\Social;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Social\MapUser;
use CakeDC\Auth\Social\Service\OAuth2Service;
use CakeDC\Auth\Social\Service\ServiceFactory;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\FacebookUser;

class MapUserTest extends TestCase
{
    /**
     * @var \\League\OAuth2\Client\Provider\Facebook&\PHPUnit\Framework\MockObject\MockObject|mixed
     */
    public $Provider;

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
                'plugin' => 'CakeDC/Users',
                'controller' => 'Users',
                'action' => 'socialLogin',
                'prefix' => null,
            ],
        ];
        Configure::write('OAuth.providers.facebook', $config);
    }

    /**
     * Test base map
     */
    public function testMap()
    {
        $service = (new ServiceFactory())->createFromProvider('facebook');

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
        $user = ['token' => $Token] + $user->toArray();
        $expected = [
            'id' => '1',
            'username' => null,
            'full_name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@gmail.com',
            'avatar' => 'https://graph.facebook.com/1/picture?type=large',
            'gender' => 'male',
            'link' => 'https://www.facebook.com/app_scoped_user_id/1/',
            'bio' => 'I am the best test user in the world.',
            'locale' => 'en_US',
            'validated' => true,
            'credentials' => [
                'token' => 'test-token',
                'secret' => null,
                'expires' => 1490988496,
            ],
            'provider' => 'facebook',
            'raw' => $user,
        ];
        $mapper = new MapUser();
        $actual = $mapper($service, $user);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test base map, invalid mapper
     */
    public function testMapInvalidMapper()
    {
        $service = new OAuth2Service([
            'className' => Facebook::class,
            'mapper' => 'CakeDC\Auth\Social\Mapper\NotExisting',
            'options' => [
                'state' => '__TEST_STATE__',
                'graphApiVersion' => 'v2.8',
                'redirectUri' => '/auth/facebook',
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
        $user = new FacebookUser([
            'id' => '1',
            'name' => 'Test User',
        ]);

        $mapper = new MapUser();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Provider mapper class CakeDC\Auth\Social\Mapper\NotExisting does not exist');
        $mapper($service, $user);
    }
}
