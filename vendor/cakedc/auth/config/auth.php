<?php
/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2018, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

use Cake\Routing\Router;
return [
    'OAuth.path' => ['plugin' => 'CakeDC/Users', 'controller' => 'Users', 'action' => 'socialLogin', 'prefix' => null],
    'OAuth.providers' => [
        'facebook' => [
            'service' => 'CakeDC\Auth\Social\Service\OAuth2Service',
            'className' => 'League\OAuth2\Client\Provider\Facebook',
            'mapper' => 'CakeDC\Auth\Social\Mapper\Facebook',
            'authParams' => ['scope' => ['public_profile', 'email', 'user_birthday', 'user_gender', 'user_link']],
            'options' => [
                'graphApiVersion' => 'v2.8', //bio field was deprecated on >= v2.8
                'redirectUri' => Router::fullBaseUrl() . '/auth/facebook',
                'linkSocialUri' => Router::fullBaseUrl() . '/link-social/facebook',
                'callbackLinkSocialUri' => Router::fullBaseUrl() . '/callback-link-social/facebook',
            ]
        ],
        'twitter' => [
            'service' => 'CakeDC\Auth\Social\Service\OAuth1Service',
            'className' => 'League\OAuth1\Client\Server\Twitter',
            'mapper' => 'CakeDC\Auth\Social\Mapper\Twitter',
            'options' => [
                'redirectUri' => Router::fullBaseUrl() . '/auth/twitter',
                'linkSocialUri' => Router::fullBaseUrl() . '/link-social/twitter',
                'callbackLinkSocialUri' => Router::fullBaseUrl() . '/callback-link-social/twitter',
            ]
        ],
        'linkedIn' => [
            'service' => 'CakeDC\Auth\Social\Service\OAuth2Service',
            'className' => 'League\OAuth2\Client\Provider\LinkedIn',
            'mapper' => 'CakeDC\Auth\Social\Mapper\LinkedIn',
            'options' => [
                'redirectUri' => Router::fullBaseUrl() . '/auth/linkedIn',
                'linkSocialUri' => Router::fullBaseUrl() . '/link-social/linkedIn',
                'callbackLinkSocialUri' => Router::fullBaseUrl() . '/callback-link-social/linkedIn',
            ]
        ],
        'instagram' => [
            'service' => 'CakeDC\Auth\Social\Service\OAuth2Service',
            'className' => 'League\OAuth2\Client\Provider\Instagram',
            'mapper' => 'CakeDC\Auth\Social\Mapper\Instagram',
            'options' => [
                'redirectUri' => Router::fullBaseUrl() . '/auth/instagram',
                'linkSocialUri' => Router::fullBaseUrl() . '/link-social/instagram',
                'callbackLinkSocialUri' => Router::fullBaseUrl() . '/callback-link-social/instagram',
            ]
        ],
        'google' => [
            'service' => 'CakeDC\Auth\Social\Service\OAuth2Service',
            'className' => 'League\OAuth2\Client\Provider\Google',
            'mapper' => 'CakeDC\Auth\Social\Mapper\Google',
            'options' => [
                'userFields' => ['url', 'aboutMe'],
                'redirectUri' => Router::fullBaseUrl() . '/auth/google',
                'linkSocialUri' => Router::fullBaseUrl() . '/link-social/google',
                'callbackLinkSocialUri' => Router::fullBaseUrl() . '/callback-link-social/google',
            ]
        ],
        'amazon' => [
            'service' => 'CakeDC\Auth\Social\Service\OAuth2Service',
            'className' => 'Luchianenco\OAuth2\Client\Provider\Amazon',
            'mapper' => 'CakeDC\Auth\Social\Mapper\Amazon',
            'options' => [
                'redirectUri' => Router::fullBaseUrl() . '/auth/amazon',
                'linkSocialUri' => Router::fullBaseUrl() . '/link-social/amazon',
                'callbackLinkSocialUri' => Router::fullBaseUrl() . '/callback-link-social/amazon',
            ]
        ],
    ],
    'OneTimePasswordAuthenticator' => [
        'checker' => \CakeDC\Auth\Authentication\DefaultOneTimePasswordAuthenticationChecker::class,
        'verifyAction' => [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'verify',
            'prefix' => false,
        ],
        'login' => false,//Enable?
        'issuer' => null,
        // The number of digits the resulting codes will be
        'digits' => 6,
        // The number of seconds a code will be valid
        'period' => 30,
        // The algorithm used
        'algorithm' => 'sha1',
        // QR-code provider (more on this later)
        'qrcodeprovider' => null,
        // Random Number Generator provider (more on this later)
        'rngprovider' => null
    ],
    'U2f' => [
        'enabled' => false,
        'checker' => \CakeDC\Auth\Authentication\DefaultU2fAuthenticationChecker::class,
        'startAction' => [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'u2f',
            'prefix' => false,
        ]
    ],
    'Webauthn2fa' => [
        'enabled' => false,
        'appName' => null,//App must set a valid name here
        'id' => null,//default value is the current domain
        'checker' => \CakeDC\Auth\Authentication\DefaultWebauthn2fAuthenticationChecker::class,
        'startAction' => [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'webauthn2fa',
            'prefix' => false,
        ]
    ]
];
