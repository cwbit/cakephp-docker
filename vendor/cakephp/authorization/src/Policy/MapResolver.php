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
namespace Authorization\Policy;

use Authorization\Policy\Exception\MissingPolicyException;
use InvalidArgumentException;

/**
 * Policy resolver that allows to map policy classes, objects or factories to
 * individual resource classes.
 */
class MapResolver implements ResolverInterface
{
    /**
     * Resource to policy class name map.
     *
     * @var array
     */
    protected $map = [];

    /**
     * Constructor.
     *
     * Takes a resource class name as a key and a policy as a value, for example:
     * ```
     * [
     *     \App\Service\Resource1::class => \App\Policy\ResourcePolicy::class,
     *     \App\Service\Resource2::class => $policyObject,
     *     \App\Service\Resource3::class => function() {},
     * ]
     * ```
     *
     * @param array $map Resource class name to policy map.
     */
    public function __construct(array $map = [])
    {
        foreach ($map as $resourceClass => $policy) {
            $this->map($resourceClass, $policy);
        }
    }

    /**
     * Maps a resource class to the policy class name.
     *
     * @param string $resourceClass A resource class name.
     * @param string|object|callable $policy A policy class name, an object or a callable factory.
     * @return $this
     * @throws \InvalidArgumentException When a resource class does not exist or policy is invalid.
     */
    public function map(string $resourceClass, $policy)
    {
        if (!class_exists($resourceClass)) {
            $message = sprintf('Resource class `%s` does not exist.', $resourceClass);
            throw new InvalidArgumentException($message);
        }

        if (!is_string($policy) && !is_object($policy) && !is_callable($policy)) {
            $message = sprintf(
                'Policy must be a valid class name, an object or a callable, `%s` given.',
                gettype($policy)
            );
            throw new InvalidArgumentException($message);
        }
        if (is_string($policy) && !class_exists($policy)) {
            $message = sprintf('Policy class `%s` does not exist.', $policy);
            throw new InvalidArgumentException($message);
        }

        $this->map[$resourceClass] = $policy;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException When a resource is not an object.
     * @throws \Authorization\Policy\Exception\MissingPolicyException When a policy for a resource has not been defined.
     */
    public function getPolicy($resource)
    {
        if (!is_object($resource)) {
            $message = sprintf('Resource must be an object, `%s` given.', gettype($resource));
            throw new InvalidArgumentException($message);
        }

        $class = get_class($resource);

        if (!isset($this->map[$class])) {
            throw new MissingPolicyException($resource);
        }

        $policy = $this->map[$class];

        if (is_callable($policy)) {
            return $policy($resource, $this);
        }

        if (is_object($policy)) {
            return $policy;
        }

        return new $policy();
    }
}
