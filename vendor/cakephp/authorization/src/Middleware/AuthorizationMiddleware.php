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

use Authentication\IdentityInterface as AuthenIdentityInterface;
use Authorization\AuthorizationServiceInterface;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Exception\AuthorizationRequiredException;
use Authorization\Exception\Exception;
use Authorization\Identity;
use Authorization\IdentityDecorator;
use Authorization\IdentityInterface;
use Authorization\Middleware\UnauthorizedHandler\HandlerFactory;
use Authorization\Middleware\UnauthorizedHandler\HandlerInterface;
use Cake\Core\InstanceConfigTrait;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

/**
 * Authorization Middleware.
 *
 * Injects the authorization service and decorated identity objects into the request object as attributes.
 */
class AuthorizationMiddleware implements MiddlewareInterface
{
    use InstanceConfigTrait;

    /**
     * Default config.
     *
     * - `identityDecorator` Identity decorator class name or a callable.
     *   Defaults to IdentityDecorator
     * - `identityAttribute` Attribute name the identity is stored under.
     *   Defaults to 'identity'
     * - `requireAuthorizationCheck` When true the middleware will raise an exception
     *   if no authorization checks were done. This aids in ensuring that all actions
     *   check authorization. It is intended as a development aid and not to be relied upon
     *   in production. Defaults to `true`.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'identityDecorator' => null,
        'identityAttribute' => 'identity',
        'requireAuthorizationCheck' => true,
        'unauthorizedHandler' => 'Authorization.Exception',
    ];

    /**
     * Authorization service or application instance.
     *
     * @var \Authorization\AuthorizationServiceInterface|\Authorization\AuthorizationServiceProviderInterface
     */
    protected $subject;

    /**
     * Constructor.
     *
     * @param \Authorization\AuthorizationServiceInterface|\Authorization\AuthorizationServiceProviderInterface $subject Authorization service or provider instance.
     * @param array $config Config array.
     * @throws \InvalidArgumentException
     */
    public function __construct($subject, array $config = [])
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if (
            !$subject instanceof AuthorizationServiceInterface &&
            !$subject instanceof AuthorizationServiceProviderInterface
        ) {
            $expected = implode('` or `', [
                AuthorizationServiceInterface::class,
                AuthorizationServiceProviderInterface::class,
            ]);
            $type = is_object($subject) ? get_class($subject) : gettype($subject);
            $message = sprintf('Subject must be an instance of `%s`, `%s` given.', $expected, $type);

            throw new InvalidArgumentException($message);
        }

        if ($this->_defaultConfig['identityDecorator'] === null) {
            $this->_defaultConfig['identityDecorator'] = interface_exists(AuthenIdentityInterface::class)
                ? Identity::class
                : IdentityDecorator::class;
        }

        $this->subject = $subject;
        $this->setConfig($config);
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
        $service = $this->getAuthorizationService($request);
        $request = $request->withAttribute('authorization', $service);

        $attribute = $this->getConfig('identityAttribute');
        $identity = $request->getAttribute($attribute);

        if ($identity !== null) {
            $identity = $this->buildIdentity($service, $identity);
            $request = $request->withAttribute($attribute, $identity);
        }

        try {
            $response = $handler->handle($request);

            if ($this->getConfig('requireAuthorizationCheck') && !$service->authorizationChecked()) {
                throw new AuthorizationRequiredException(['url' => $request->getRequestTarget()]);
            }
        } catch (Exception $exception) {
            $unauthorizedHandler = $this->getHandler();
            $response = $unauthorizedHandler->handle(
                $exception,
                $request,
                (array)$this->getConfig('unauthorizedHandler')
            );
        }

        return $response;
    }

    /**
     * Returns unauthorized handler.
     *
     * @return \Authorization\Middleware\UnauthorizedHandler\HandlerInterface
     */
    protected function getHandler(): HandlerInterface
    {
        $handler = $this->getConfig('unauthorizedHandler');
        if (!is_array($handler)) {
            $handler = [
                'className' => $handler,
            ];
        }
        if (!isset($handler['className'])) {
            throw new RuntimeException('Missing `className` key from handler config.');
        }

        return HandlerFactory::create($handler['className']);
    }

    /**
     * Returns AuthorizationServiceInterface instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request.
     * @return \Authorization\AuthorizationServiceInterface
     * @throws \RuntimeException When authorization method has not been defined.
     */
    protected function getAuthorizationService(
        ServerRequestInterface $request
    ): AuthorizationServiceInterface {
        $service = $this->subject;
        if ($this->subject instanceof AuthorizationServiceProviderInterface) {
            $service = $this->subject->getAuthorizationService($request);
        }

        if (!$service instanceof AuthorizationServiceInterface) {
            throw new RuntimeException(sprintf(
                'Invalid service returned from the provider. `%s` does not implement `%s`.',
                getTypeName($service),
                AuthorizationServiceInterface::class
            ));
        }

        return $service;
    }

    /**
     * Builds the identity object.
     *
     * @param \Authorization\AuthorizationServiceInterface $service Authorization service.
     * @param \ArrayAccess|array $identity Identity data
     * @return \Authorization\IdentityInterface
     */
    protected function buildIdentity(AuthorizationServiceInterface $service, $identity): IdentityInterface
    {
        $class = $this->getConfig('identityDecorator');

        if (is_callable($class)) {
            $identity = $class($service, $identity);
        } else {
            if (!$identity instanceof IdentityInterface) {
                $identity = new $class($service, $identity);
            }
        }

        if (!$identity instanceof IdentityInterface) {
            throw new RuntimeException(sprintf(
                'Invalid identity returned by decorator. `%s` does not implement `%s`.',
                is_object($identity) ? get_class($identity) : gettype($identity),
                IdentityInterface::class
            ));
        }

        return $identity;
    }
}
