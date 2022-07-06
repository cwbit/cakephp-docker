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
namespace Authorization\Controller\Component;

use Authorization\AuthorizationServiceInterface;
use Authorization\Exception\ForbiddenException;
use Authorization\Exception\MissingIdentityException;
use Authorization\IdentityInterface;
use Authorization\Policy\ResultInterface;
use Cake\Controller\Component;
use Cake\Http\ServerRequest;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;

/**
 * Authorization Component
 *
 * Makes it easier to check authorization in CakePHP controllers.
 * Applies conventions on matching policy methods to controller actions,
 * and raising errors when authorization fails.
 */
class AuthorizationComponent extends Component
{
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = [
        'identityAttribute' => 'identity',
        'serviceAttribute' => 'authorization',
        'authorizationEvent' => 'Controller.startup',
        'skipAuthorization' => [],
        'authorizeModel' => [],
        'actionMap' => [],
    ];

    /**
     * Check the policy for $resource, raising an exception on error.
     *
     * If $action is left undefined, the current controller action will
     * be used.
     *
     * @param mixed $resource The resource to check authorization on.
     * @param string|null $action The action to check authorization for.
     * @return void
     * @throws \Authorization\Exception\ForbiddenException when policy check fails.
     */
    public function authorize($resource, ?string $action = null): void
    {
        if ($action === null) {
            $request = $this->getController()->getRequest();
            $action = $this->getDefaultAction($request);
        }

        $result = $this->canResult($resource, $action);
        if ($result->getStatus()) {
            return;
        }

        if (is_object($resource)) {
            $name = get_class($resource);
        } elseif (is_string($resource)) {
            $name = $resource;
        } else {
            $name = gettype($resource);
        }
        throw new ForbiddenException($result, [$action, $name]);
    }

    /**
     * Check the policy for $resource, returns true if the action is allowed
     *
     * If $action is left undefined, the current controller action will
     * be used.
     *
     * @param mixed $resource The resource to check authorization on.
     * @param string|null $action The action to check authorization for.
     * @return bool
     * @psalm-suppress InvalidReturnType
     */
    public function can($resource, ?string $action = null): bool
    {
        /** @psalm-suppress InvalidReturnStatement */
        return $this->performCheck($resource, $action);
    }

    /**
     * Check the policy for $resource, returns true if the action is allowed
     *
     * If $action is left undefined, the current controller action will
     * be used.
     *
     * @param mixed $resource The resource to check authorization on.
     * @param string|null $action The action to check authorization for.
     * @return \Authorization\Policy\ResultInterface
     * @psalm-suppress InvalidReturnType
     */
    public function canResult($resource, ?string $action = null): ResultInterface
    {
        /** @psalm-suppress InvalidReturnStatement */
        return $this->performCheck($resource, $action, 'canResult');
    }

    /**
     * Check the policy for $resource.
     *
     * @param mixed $resource The resource to check authorization on.
     * @param string|null $action The action to check authorization for.
     * @param string $method The method to use, either "can" or "canResult".
     * @return bool|\Authorization\Policy\ResultInterface
     */
    protected function performCheck($resource, ?string $action = null, string $method = 'can')
    {
        $request = $this->getController()->getRequest();
        if ($action === null) {
            $action = $this->getDefaultAction($request);
        }

        $identity = $this->getIdentity($request);
        if (empty($identity)) {
            return $this->getService($request)->{$method}(null, $action, $resource);
        }

        return $identity->{$method}($action, $resource);
    }

    /**
     * Applies a scope for $resource.
     *
     * If $action is left undefined, the current controller action will
     * be used.
     *
     * @param mixed $resource The resource to apply a scope to.
     * @param string|null $action The action to apply a scope for.
     * @return mixed
     */
    public function applyScope($resource, ?string $action = null)
    {
        $request = $this->getController()->getRequest();
        if ($action === null) {
            $action = $this->getDefaultAction($request);
        }
        $identity = $this->getIdentity($request);
        if ($identity === null) {
            throw new MissingIdentityException('Identity must exist for applyScope() call.');
        }

        return $identity->applyScope($action, $resource);
    }

    /**
     * Skips the authorization check.
     *
     * @return $this
     */
    public function skipAuthorization()
    {
        $request = $this->getController()->getRequest();
        $service = $this->getService($request);

        $service->skipAuthorization();

        return $this;
    }

    /**
     * Allows to map controller action to another authorization policy action.
     *
     * For instance you may want to authorize `add` action with `create` authorization policy.
     *
     * @param string $controllerAction Controller action.
     * @param string $policyAction Policy action.
     * @return $this
     */
    public function mapAction(string $controllerAction, string $policyAction)
    {
        $this->_config['actionMap'][$controllerAction] = $policyAction;

        return $this;
    }

    /**
     * Allows to map controller actions to policy actions.
     *
     * @param array $actions Map of controller action to policy action.
     * @param bool $overwrite Set to true to override configuration. False will merge with current configuration.
     * @return $this
     */
    public function mapActions(array $actions, bool $overwrite = false)
    {
        $this->setConfig('actionMap', $actions, !$overwrite);

        return $this;
    }

    /**
     * Adds an action to automatic model authorization checks.
     *
     * @param string ...$actions Controller action to authorize against table policy.
     * @return $this
     */
    public function authorizeModel(string ...$actions)
    {
        $this->_config['authorizeModel'] = array_merge($this->_config['authorizeModel'], $actions);

        return $this;
    }

    /**
     * Get the authorization service from a request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request
     * @return \Authorization\AuthorizationServiceInterface
     * @throws \InvalidArgumentException When invalid authorization service encountered.
     */
    protected function getService(ServerRequestInterface $request): \Authorization\AuthorizationServiceInterface
    {
        $serviceAttribute = $this->getConfig('serviceAttribute');
        $service = $request->getAttribute($serviceAttribute);
        if (!$service instanceof AuthorizationServiceInterface) {
            $type = is_object($service) ? get_class($service) : gettype($service);
            throw new InvalidArgumentException(sprintf(
                'Expected that `%s` would be an instance of %s, but got %s',
                $serviceAttribute,
                AuthorizationServiceInterface::class,
                $type
            ));
        }

        return $service;
    }

    /**
     * Get the identity from a request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request
     * @return \Authorization\IdentityInterface|null
     * @throws \Authorization\Exception\MissingIdentityException When identity is not present in a request.
     * @throws \InvalidArgumentException When invalid identity encountered.
     */
    protected function getIdentity(ServerRequestInterface $request): ?IdentityInterface
    {
        $identityAttribute = $this->getConfig('identityAttribute');
        $identity = $request->getAttribute($identityAttribute);
        if ($identity === null) {
            return $identity;
        }
        if (!$identity instanceof IdentityInterface) {
            $type = is_object($identity) ? get_class($identity) : gettype($identity);
            throw new InvalidArgumentException(sprintf(
                'Expected that `%s` would be an instance of %s, but got %s',
                $identityAttribute,
                IdentityInterface::class,
                $type
            ));
        }

        return $identity;
    }

    /**
     * Action authorization handler.
     *
     * Checks identity and model authorization.
     *
     * @return void
     */
    public function authorizeAction(): void
    {
        $request = $this->getController()->getRequest();
        $action = $request->getParam('action');

        $skipAuthorization = $this->checkAction($action, 'skipAuthorization');
        if ($skipAuthorization) {
            $this->skipAuthorization();

            return;
        }

        $authorizeModel = $this->checkAction($action, 'authorizeModel');
        if ($authorizeModel) {
            $this->authorize($this->getController()->loadModel());
        }
    }

    /**
     * Checks whether an action should be authorized according to the config key provided.
     *
     * @param string $action Action name.
     * @param string $configKey Configuration key with actions.
     * @return bool
     */
    protected function checkAction(string $action, string $configKey): bool
    {
        $actions = (array)$this->getConfig($configKey);

        return in_array($action, $actions, true);
    }

    /**
     * Returns authorization action name for a controller action resolved from the request.
     *
     * @param \Cake\Http\ServerRequest $request Server request.
     * @return string
     * @throws \UnexpectedValueException When invalid action type encountered.
     */
    protected function getDefaultAction(ServerRequest $request): string
    {
        $action = $request->getParam('action');
        $name = $this->getConfig('actionMap.' . $action);

        if ($name === null) {
            return $action;
        }
        if (!is_string($name)) {
            $type = is_object($name) ? get_class($name) : gettype($name);
            $message = sprintf('Invalid action type for `%s`. Expected `string` or `null`, got `%s`.', $action, $type);
            throw new UnexpectedValueException($message);
        }

        return $name;
    }

    /**
     * Returns model authorization handler if model authorization is enabled.
     *
     * @return array<string, mixed>
     */
    public function implementedEvents(): array
    {
        return [
            $this->getConfig('authorizationEvent') => 'authorizeAction',
        ];
    }
}
