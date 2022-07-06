<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Authorization\Middleware;

use Authorization\AuthorizationServiceInterface;
use Authorization\Exception\ForbiddenException;
use Cake\Core\InstanceConfigTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

/**
 * Request Authorization Middleware
 *
 * This MUST be added after the Authorization, Authentication and
 * RoutingMiddleware in the Middleware Queue!
 *
 * This middleware is useful when you want to authorize your requests, for example
 * each controller and action, against a role based access system or any other
 * kind of authorization process that controls access to certain actions.
 */
class RequestAuthorizationMiddleware implements MiddlewareInterface
{
    use InstanceConfigTrait;

    /**
     * Default Config
     *
     * @var array
     */
    protected $_defaultConfig = [
        'authorizationAttribute' => 'authorization',
        'identityAttribute' => 'identity',
        'method' => 'access',
    ];

    /**
     * Constructor
     *
     * @param array $config Configuration options
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Gets the authorization service from the request attribute
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request.
     * @return \Authorization\AuthorizationServiceInterface
     */
    protected function getServiceFromRequest(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        $serviceAttribute = $this->getConfig('authorizationAttribute');
        $service = $request->getAttribute($serviceAttribute);

        if (!$service instanceof AuthorizationServiceInterface) {
            $errorMessage = self::class . ' could not find the authorization service in the request attribute. ' .
                'Make sure you added the AuthorizationMiddleware before this middleware or that you ' .
                'somehow else added the service to the requests `' . $serviceAttribute . '` attribute.';

            throw new RuntimeException($errorMessage);
        }

        return $service;
    }

    /**
     * Callable implementation for the middleware stack.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $service = $this->getServiceFromRequest($request);
        $identity = $request->getAttribute($this->getConfig('identityAttribute'));

        $result = $service->canResult($identity, $this->getConfig('method'), $request);
        if (!$result->getStatus()) {
            throw new ForbiddenException($result);
        }

        return $handler->handle($request);
    }
}
