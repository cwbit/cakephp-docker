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

use Cake\Http\Exception\BadRequestException;
use Cake\Http\ServerRequest;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ServerRequestInterface;

class OAuth2Service extends OAuthServiceAbstract
{
    /**
     * @var \League\OAuth2\Client\Provider\AbstractProvider
     */
    protected $provider;

    /**
     * OAuth2Service constructor.
     *
     * @param array $providerConfig with className and options keys
     */
    public function __construct(array $providerConfig)
    {
        $this->setProvider($providerConfig);
        $this->setConfig($providerConfig);
    }

    /**
     * Check if we are at getUserStep, meaning, we received a callback from provider.
     * Return true when querystring code is not empty
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return bool
     */
    public function isGetUserStep(ServerRequestInterface $request): bool
    {
        if (!$request instanceof ServerRequest) {
            throw new \BadMethodCallException('Request must be an instance of ServerRequest');
        }

        return !empty($request->getQuery('code'));
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
        if ($this->getConfig('options.state')) {
            $request->getSession()->write('oauth2state', $this->provider->getState());
        }

        return $this->provider->getAuthorizationUrl(
            $this->getConfig('authParams', [])
        );
    }

    /**
     * Get a user in social provider
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @throws \Cake\Http\Exception\BadRequestException when oauth2 state is invalid
     * @return array
     */
    public function getUser(ServerRequestInterface $request): array
    {
        if (!$request instanceof ServerRequest) {
            throw new \BadMethodCallException('Request must be an instance of ServerRequest');
        }
        if (!$this->validate($request)) {
            throw new BadRequestException('Invalid OAuth2 state');
        }

        $code = $request->getQuery('code');
        /** @var \League\OAuth2\Client\Token\AccessToken $token */
        $token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);

        return ['token' => $token] + $this->provider->getResourceOwner($token)->toArray();
    }

    /**
     * Validates OAuth2 request.
     *
     * @param \Cake\Http\ServerRequest $request Request object.
     * @return bool
     */
    protected function validate(ServerRequest $request)
    {
        if (!array_key_exists('code', $request->getQueryParams())) {
            return false;
        }

        $session = $request->getSession();
        $sessionKey = 'oauth2state';
        $state = $request->getQuery('state');

        if (
            $this->getConfig('options.state') &&
            (!$state ||
            $state !== $session->read($sessionKey))
        ) {
            $session->delete($sessionKey);

            return false;
        }

        return true;
    }

    /**
     * Instantiates provider object.
     *
     * @param array $config for provider.
     * @return void
     */
    protected function setProvider($config)
    {
        if (is_object($config['className']) && $config['className'] instanceof AbstractProvider) {
            $this->provider = $config['className'];
        } else {
            $class = $config['className'];

            $this->provider = new $class($config['options'], $config['collaborators']);
        }
    }
}
