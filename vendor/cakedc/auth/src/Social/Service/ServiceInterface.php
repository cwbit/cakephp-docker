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

use Psr\Http\Message\ServerRequestInterface;

interface ServiceInterface
{
    /**
     * Check if we are at getUserStep, meaning, we received a callback from provider.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return bool
     */
    public function isGetUserStep(ServerRequestInterface $request): bool;

    /**
     * Get a authentication url for user
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return string
     */
    public function getAuthorizationUrl(ServerRequestInterface $request);

    /**
     * Get a user in social provider
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return array
     */
    public function getUser(ServerRequestInterface $request): array;

    /**
     * Get the provider name
     *
     * @return string
     */
    public function getProviderName(): string;

    /**
     * Set the provider name
     *
     * @param string $name set name
     * @return self
     */
    public function setProviderName(string $name);

    /**
     * Get current config
     *
     * @param string|null $key The key to get or null for the whole config.
     * @param mixed $default The return value when the key does not exist.
     * @return mixed Config value being read.
     */
    public function getConfig(?string $key = null, $default = null);
}
