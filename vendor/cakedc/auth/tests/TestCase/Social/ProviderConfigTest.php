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
use CakeDC\Auth\Exception\InvalidProviderException;
use CakeDC\Auth\Exception\InvalidSettingsException;
use CakeDC\Auth\Social\ProviderConfig;

/**
 * Users\Social\ProviderConfig Test Case
 */
class ProviderConfigTest extends TestCase
{
    /**
     * Test with invalid provider class
     *
     * @return void
     */
    public function testWithInvalidProviderClass()
    {
        Configure::write('OAuth.providers.facebook.options.clientId', '10003030300303');
        Configure::write('OAuth.providers.facebook.options.clientSecret', 'secretpassword');
        Configure::write('OAuth.providers.facebook.className', 'League\OAuth2\Client\Provider\InvalidFacebook');

        $this->expectException(InvalidProviderException::class);
        new ProviderConfig();
    }

    /**
     * Test with invalid service class
     *
     * @return void
     */
    public function testWithInvalidServiceClass()
    {
        Configure::write('OAuth.providers.facebook.options.clientId', '10003030300303');
        Configure::write('OAuth.providers.facebook.options.clientSecret', 'secretpassword');
        Configure::write('OAuth.providers.facebook.service', 'CakeDC\Auth\Social\Service\InvalidOAuth2Service');

        $this->expectException(InvalidProviderException::class);
        new ProviderConfig();
    }

    /**
     * Test with invalid mapper class
     *
     * @return void
     */
    public function testWithInvalidMapperClass()
    {
        Configure::write('OAuth.providers.facebook.options.clientId', '10003030300303');
        Configure::write('OAuth.providers.facebook.options.clientSecret', 'secretpassword');
        Configure::write('OAuth.providers.facebook.mapper', 'CakeDC\Auth\Social\Mapper\InvalidFacebook');

        $this->expectException(InvalidProviderException::class);
        new ProviderConfig();
    }

    /**
     * Test with invalid settings options value type
     *
     * @return void
     */
    public function testWithInvalidOptionsValueType()
    {
        $this->expectException(InvalidSettingsException::class);
        $config = [
            'path' => [
                'plugin' => 'CakeDC/Users',
                'controller' => 'Users',
                'action' => 'socialLogin',
                'prefix' => null,
            ],
            'providers' => [
                'facebook' => [
                    'service' => \CakeDC\Auth\Social\Service\OAuth2Service::class,
                    'className' => \League\OAuth2\Client\Provider\Facebook::class,
                    'mapper' => \CakeDC\Auth\Social\Mapper\Facebook::class,
                    'options' => 'invalid options',
                ],
            ],
        ];
        (new ProviderConfig())->normalizeConfig($config);
    }

    /**
     * Test with invalid settings collaborators value type
     *
     * @return void
     */
    public function testWithInvalidCollaboratorsValueType()
    {
        Configure::write('OAuth.providers.facebook.options.clientId', '10003030300303');
        Configure::write('OAuth.providers.facebook.options.clientSecret', 'secretpassword');
        Configure::write('OAuth.providers.facebook.collaborators', 'johndoe');

        $this->expectException(InvalidSettingsException::class);
        new ProviderConfig();
    }

    /**
     * Test with custom config
     *
     * @return void
     */
    public function testWithCustomConfig()
    {
        Configure::write('OAuth.providers.facebook.options.clientId', '10003030300303');
        Configure::write('OAuth.providers.facebook.options.clientSecret', 'secretpassword');
        Configure::write('OAuth.providers.twitter.options.clientId', '20003030300303');
        Configure::write('OAuth.providers.twitter.options.clientSecret', 'weakpassword');
        Configure::write('OAuth.providers.amazon.options.clientId', '3003030300303');
        Configure::write('OAuth.providers.amazon.options.clientSecret', 'weaksecretpassword');

        $Config = new ProviderConfig([
            'options' => [
                'customOption' => 'hello',
            ],
        ]);
        $expected = [
            'service' => \CakeDC\Auth\Social\Service\OAuth2Service::class,
            'className' => \League\OAuth2\Client\Provider\Facebook::class,
            'mapper' => \CakeDC\Auth\Social\Mapper\Facebook::class,
            'authParams' => ['scope' => ['public_profile', 'email', 'user_birthday', 'user_gender', 'user_link']],
            'options' => [
                'customOption' => 'hello',
                'graphApiVersion' => 'v2.8',
                'redirectUri' => 'http://localhost/auth/facebook',
                'linkSocialUri' => 'http://localhost/link-social/facebook',
                'callbackLinkSocialUri' => 'http://localhost/callback-link-social/facebook',
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
        $actual = $Config->getConfig('facebook');
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test with providers enabled
     *
     * @return void
     */
    public function testWithProvidersEnabled()
    {
        Configure::write('OAuth.providers.facebook.options.clientId', '10003030300303');
        Configure::write('OAuth.providers.facebook.options.clientSecret', 'secretpassword');
        Configure::write('OAuth.providers.twitter.options.clientId', '20003030300303');
        Configure::write('OAuth.providers.twitter.options.clientSecret', 'weakpassword');
        Configure::write('OAuth.providers.amazon.options.clientId', '3003030300303');
        Configure::write('OAuth.providers.amazon.options.clientSecret', 'weaksecretpassword');

        $Config = new ProviderConfig();
        $expected = [
            'service' => \CakeDC\Auth\Social\Service\OAuth2Service::class,
            'className' => \League\OAuth2\Client\Provider\Facebook::class,
            'mapper' => \CakeDC\Auth\Social\Mapper\Facebook::class,
            'authParams' => ['scope' => ['public_profile', 'email', 'user_birthday', 'user_gender', 'user_link']],
            'options' => [
                'graphApiVersion' => 'v2.8',
                'redirectUri' => 'http://localhost/auth/facebook',
                'linkSocialUri' => 'http://localhost/link-social/facebook',
                'callbackLinkSocialUri' => 'http://localhost/callback-link-social/facebook',
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
        $actual = $Config->getConfig('facebook');

        $this->assertEquals($expected, $actual);

        $expected = [
            'service' => \CakeDC\Auth\Social\Service\OAuth1Service::class,
            'className' => \League\OAuth1\Client\Server\Twitter::class,
            'mapper' => \CakeDC\Auth\Social\Mapper\Twitter::class,
            'authParams' => [],
            'options' => [
                'redirectUri' => 'http://localhost/auth/twitter',
                'linkSocialUri' => 'http://localhost/link-social/twitter',
                'callbackLinkSocialUri' => 'http://localhost/callback-link-social/twitter',
                'clientId' => '20003030300303',
                'clientSecret' => 'weakpassword',
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
        $actual = $Config->getConfig('twitter');
        $this->assertEquals($expected, $actual);

        $expected = [
            'service' => \CakeDC\Auth\Social\Service\OAuth2Service::class,
            'className' => \Luchianenco\OAuth2\Client\Provider\Amazon::class,
            'mapper' => \CakeDC\Auth\Social\Mapper\Amazon::class,
            'authParams' => [],
            'options' => [
                'redirectUri' => 'http://localhost/auth/amazon',
                'linkSocialUri' => 'http://localhost/link-social/amazon',
                'callbackLinkSocialUri' => 'http://localhost/callback-link-social/amazon',
                'clientId' => '3003030300303',
                'clientSecret' => 'weaksecretpassword',
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
        $actual = $Config->getConfig('amazon');
        $this->assertEquals($expected, $actual);

        $expected = [];
        $actual = $Config->getConfig('linkedIn');
        $this->assertEquals($expected, $actual);

        $expected = [];
        $actual = $Config->getConfig('instagram');
        $this->assertEquals($expected, $actual);

        $expected = [];
        $actual = $Config->getConfig('google');
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test without providers enabled
     *
     * @return void
     */
    public function testWithoutProvidersEnabled()
    {
        $Config = new ProviderConfig();
        $expected = [];
        $actual = $Config->getConfig('facebook');
        $this->assertEquals($expected, $actual);

        $expected = [];
        $actual = $Config->getConfig('twitter');
        $this->assertEquals($expected, $actual);

        $expected = [];
        $actual = $Config->getConfig('amazon');
        $this->assertEquals($expected, $actual);

        $expected = [];
        $actual = $Config->getConfig('linkedIn');
        $this->assertEquals($expected, $actual);

        $expected = [];
        $actual = $Config->getConfig('instagram');
        $this->assertEquals($expected, $actual);

        $expected = [];
        $actual = $Config->getConfig('google');
        $this->assertEquals($expected, $actual);
    }
}
