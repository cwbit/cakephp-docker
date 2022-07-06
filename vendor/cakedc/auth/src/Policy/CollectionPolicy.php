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

namespace CakeDC\Auth\Policy;

use Authorization\IdentityInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CollectionPolicy
 *
 * @package CakeDC\Auth\Policy
 */
class CollectionPolicy
{
    /**
     * List of policies
     *
     * @var array
     */
    protected $policies;

    /**
     * CollectionPolicy constructor.
     *
     * @param array $policies List of policies.
     */
    public function __construct(array $policies)
    {
        $this->policies = $policies;
    }

    /**
     * Check permission, stop at first success from $policies or when all fails
     *
     * @param \Authorization\IdentityInterface|null $identity user identity
     * @param \Psr\Http\Message\ServerRequestInterface $resource server request
     * @return bool
     */
    public function canAccess(?IdentityInterface $identity, ServerRequestInterface $resource): bool
    {
        foreach ($this->policies as $policy => $config) {
            if (!is_array($config)) {
                $policy = $config;
                $config = [];
            }
            if (is_string($policy)) {
                $policy = new $policy($config);
            }

            if ($policy instanceof PolicyInterface && $policy->canAccess($identity, $resource)) {
                return true;
            }
        }

        return false;
    }
}
