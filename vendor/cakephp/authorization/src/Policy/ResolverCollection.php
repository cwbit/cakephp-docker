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

/**
 * `ResolverCollection` is used for aggregating multiple resolvers when more than
 * one resolver is necessary. The collection will iterate over configured resolvers
 * and try to resolve a policy on each one. The first successfully resolved policy
 * will be returned.
 *
 * Configured resolvers must throw `Authorization\Policy\Exception\MissingPolicyException`
 * if a policy cannot be resolved.
 *
 * Example configuration:
 *
 * ```
 * $collection = new ResolverCollection([
 *     new OrmResolver(),
 *     new MapResolver([
 *         Service::class => ServicePolicy::class
 *     ])
 * ]);
 *
 * $service = new AuthorizationService($collection);
 * ```
 */
class ResolverCollection implements ResolverInterface
{
    /**
     * Policy resolver instances.
     *
     * @var \Authorization\Policy\ResolverInterface[]
     */
    protected $resolvers = [];

    /**
     * Constructor. Takes an array of policy resolver instances.
     *
     * @param \Authorization\Policy\ResolverInterface[] $resolvers An array of policy resolver instances.
     */
    public function __construct(array $resolvers = [])
    {
        foreach ($resolvers as $resolver) {
            $this->add($resolver);
        }
    }

    /**
     * Adds a resolver to the collection.
     *
     * @param \Authorization\Policy\ResolverInterface $resolver Resolver instance.
     * @return $this
     */
    public function add(ResolverInterface $resolver)
    {
        $this->resolvers[] = $resolver;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPolicy($resource)
    {
        foreach ($this->resolvers as $resolver) {
            try {
                return $resolver->getPolicy($resource);
            } catch (MissingPolicyException $e) {
            }
        }

        throw new MissingPolicyException($resource);
    }
}
