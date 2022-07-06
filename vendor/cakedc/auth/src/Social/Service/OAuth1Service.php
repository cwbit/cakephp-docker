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

namespace CakeDC\Auth\Social\Service;

use Cake\Http\ServerRequest;
use League\OAuth1\Client\Server\Server;
use Psr\Http\Message\ServerRequestInterface;

class OAuth1Service extends OAuthServiceAbstract
{
    /**
     * @var \League\OAuth1\Client\Server\Server
     */
    protected $provider;

    /**
     * OAuth2Service constructor.
     *
     * @param array $providerConfig with className and options keys
     */
    public function __construct(array $providerConfig)
    {
        $map = [
            'identifier' => 'clientId',
            'secret' => 'clientSecret',
            'callback_uri' => 'redirectUri',
        ];

        foreach ($map as $to => $from) {
            if (array_key_exists($from, $providerConfig['options'])) {
                $providerConfig['options'][$to] = $providerConfig['options'][$from];
                unset($providerConfig['options'][$from]);
            }
        }
        $providerConfig += ['signature' => null];
        $this->setProvider($providerConfig);
        $this->setConfig($providerConfig);
    }

    /**
     * Check if we are at getUserStep, meaning, we received a callback from provider.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return bool
     */
    public function isGetUserStep(ServerRequestInterface $request): bool
    {
        if (!$request instanceof ServerRequest) {
            throw new \BadMethodCallException('Request must be an instance of ServerRequest');
        }
        $oauthToken = $request->getQuery('oauth_token');
        $oauthVerifier = $request->getQuery('oauth_verifier');

        return !empty($oauthToken) && !empty($oauthVerifier);
    }

    /**
     * Get a authentication url for user
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return string
     */
    public function getAuthorizationUrl(ServerRequestInterface $request)
    {
        if (!$request instanceof ServerRequest) {
            throw new \BadMethodCallException('Request must be an instance of ServerRequest');
        }
        $temporaryCredentials = $this->provider->getTemporaryCredentials();
        $request->getSession()->write('temporary_credentials', $temporaryCredentials);

        return $this->provider->getAuthorizationUrl($temporaryCredentials);
    }

    /**
     * Get a user in social provider
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return array
     */
    public function getUser(ServerRequestInterface $request): array
    {
        if (!$request instanceof ServerRequest) {
            throw new \BadMethodCallException('Request must be an instance of ServerRequest');
        }
        $oauthToken = $request->getQuery('oauth_token');
        $oauthVerifier = $request->getQuery('oauth_verifier');
        $oauthToken = is_string($oauthToken) ? $oauthToken : '';
        $oauthVerifier = is_string($oauthVerifier) ? $oauthVerifier : '';
        /**
         * @var \League\OAuth1\Client\Credentials\TemporaryCredentials $temporaryCredentials
         */
        $temporaryCredentials = $request->getSession()->read('temporary_credentials');
        $tokenCredentials = $this->provider->getTokenCredentials($temporaryCredentials, $oauthToken, $oauthVerifier);
        $user = (array)$this->provider->getUserDetails($tokenCredentials);
        $user['token'] = [
            'accessToken' => $tokenCredentials->getIdentifier(),
            'tokenSecret' => $tokenCredentials->getSecret(),
        ];

        return $user;
    }

    /**
     * Instantiates provider object.
     *
     * @param array $config for provider.
     * @return void
     */
    protected function setProvider($config)
    {
        if (is_object($config['className']) && $config['className'] instanceof Server) {
            $this->provider = $config['className'];
        } else {
            $class = $config['className'];

            $this->provider = new $class($config['options'], $config['signature']);
        }
    }
}
