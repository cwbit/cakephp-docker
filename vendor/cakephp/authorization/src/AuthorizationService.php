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
namespace Authorization;

use Authorization\Exception\Exception;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Exception\MissingMethodException;
use Authorization\Policy\ResolverInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

class AuthorizationService implements AuthorizationServiceInterface
{
    /**
     * Authorization policy resolver.
     *
     * @var \Authorization\Policy\ResolverInterface
     */
    protected $resolver;

    /**
     * Track whether or not authorization was checked.
     *
     * @var bool
     */
    protected $authorizationChecked = false;

    /**
     * @param \Authorization\Policy\ResolverInterface $resolver Authorization policy resolver.
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @inheritDoc
     */
    public function can(?IdentityInterface $user, string $action, $resource): bool
    {
        $result = $this->performCheck($user, $action, $resource);

        return is_bool($result) ? $result : $result->getStatus();
    }

    /**
     * @inheritDoc
     */
    public function canResult(?IdentityInterface $user, string $action, $resource): ResultInterface
    {
        $result = $this->performCheck($user, $action, $resource);

        return is_bool($result) ? new Result($result) : $result;
    }

    /**
     * Check whether the provided user can perform an action on a resource.
     *
     * @param \Authorization\IdentityInterface|null $user The user to check permissions for.
     * @param string $action The action/operation being performed.
     * @param mixed $resource The resource being operated on.
     * @return bool|\Authorization\Policy\ResultInterface
     */
    protected function performCheck(?IdentityInterface $user, string $action, $resource)
    {
        $this->authorizationChecked = true;
        $policy = $this->resolver->getPolicy($resource);

        if ($policy instanceof BeforePolicyInterface) {
            $result = $policy->before($user, $resource, $action);

            if ($result !== null) {
                return $this->resultTypeCheck($result);
            }
        }

        $handler = $this->getCanHandler($policy, $action);
        $result = $handler($user, $resource);

        return $this->resultTypeCheck($result);
    }

    /**
     * Check result type.
     *
     * @param mixed $result Result from policy class instance.
     * @return bool|\Authorization\Policy\ResultInterface
     * @throws \Authorization\Exception\Exception If $result argument is not a boolean or ResultInterface instance.
     */
    protected function resultTypeCheck($result)
    {
        if (is_bool($result) || $result instanceof ResultInterface) {
            return $result;
        }

        throw new Exception(sprintf(
            'Pre-authorization check must return `%s`, `bool` or `null`.',
            ResultInterface::class
        ));
    }

    /**
     * @inheritDoc
     */
    public function applyScope(?IdentityInterface $user, string $action, $resource)
    {
        $this->authorizationChecked = true;
        $policy = $this->resolver->getPolicy($resource);
        $handler = $this->getScopeHandler($policy, $action);

        return $handler($user, $resource);
    }

    /**
     * Returns a policy action handler.
     *
     * @param mixed $policy Policy object.
     * @param string $action Action name.
     * @return callable
     * @throws \Authorization\Policy\Exception\MissingMethodException
     */
    protected function getCanHandler($policy, $action): callable
    {
        $method = 'can' . ucfirst($action);

        if (!method_exists($policy, $method) && !method_exists($policy, '__call')) {
            throw new MissingMethodException([$method, $action, get_class($policy)]);
        }

        return [$policy, $method];
    }

    /**
     * Returns a policy scope action handler.
     *
     * @param mixed $policy Policy object.
     * @param string $action Action name.
     * @return callable
     * @throws \Authorization\Policy\Exception\MissingMethodException
     */
    protected function getScopeHandler($policy, $action): callable
    {
        $method = 'scope' . ucfirst($action);

        if (!method_exists($policy, $method)) {
            throw new MissingMethodException([$method, $action, get_class($policy)]);
        }

        return [$policy, $method];
    }

    /**
     * @inheritDoc
     */
    public function authorizationChecked(): bool
    {
        return $this->authorizationChecked;
    }

    /**
     * @inheritDoc
     */
    public function skipAuthorization()
    {
        $this->authorizationChecked = true;

        return $this;
    }
}
