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
use Cake\Core\InstanceConfigTrait;
use CakeDC\Auth\Rbac\Rbac;
use CakeDC\Auth\Rbac\RbacInterface;
use Psr\Http\Message\ServerRequestInterface;

class RbacPolicy implements PolicyInterface
{
    use InstanceConfigTrait;

    /**
     * The default config.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'adapter' => [
            'className' => Rbac::class,
        ],
    ];

    /**
     * Cached instance of the rbac, we don't need to build more than 1 per request
     *
     * @var \CakeDC\Auth\Rbac\RbacInterface|null
     */
    protected $createdRbac;

    /**
     * RbacPolicy constructor.
     *
     * @param array $config Policy configurations
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Check rbac permission
     *
     * @param \Authorization\IdentityInterface|null $identity user identity
     * @param \Psr\Http\Message\ServerRequestInterface $resource server request
     * @return bool
     */
    public function canAccess(?IdentityInterface $identity, ServerRequestInterface $resource): bool
    {
        $rbac = $this->getRbac($resource);

        $user = $identity !== null ? $identity->getOriginalData() : [];

        return (bool)$rbac->checkPermissions($user, $resource);
    }

    /**
     * Get the rbac object from source or create a new one, only 1 Rbac instance will be
     * created per request
     *
     * @param \Psr\Http\Message\ServerRequestInterface $resource server request
     * @return \CakeDC\Auth\Rbac\RbacInterface
     */
    public function getRbac($resource): RbacInterface
    {
        $rbac = $resource->getAttribute('rbac');
        if ($rbac !== null) {
            return $rbac;
        }

        $adapter = $this->getConfig('adapter');
        if (is_array($adapter)) {
            if ($this->createdRbac) {
                return $this->createdRbac;
            }
            $this->createdRbac = $this->createRbac($adapter);

            return $this->createdRbac;
        }

        return $adapter;
    }

    /**
     * Create an instance of Rbac
     *
     * @param array $config Rbac config
     * @throws \InvalidArgumentException When 'key' className is missing in $config
     * @return \CakeDC\Auth\Rbac\RbacInterface
     */
    protected function createRbac($config): RbacInterface
    {
        if (isset($config['className'])) {
            $className = $config['className'];
            unset($config['className']);

            $rbac = new $className($config);
            if ($rbac instanceof RbacInterface) {
                return $rbac;
            }
        }

        throw new \InvalidArgumentException('Config "adapter" should be an object or an array with key className');
    }
}
