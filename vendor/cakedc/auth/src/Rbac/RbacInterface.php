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

namespace CakeDC\Auth\Rbac;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Rbac, determine if a request matches any of the given rbac rules
 *
 * @package Rbac
 */
interface RbacInterface
{
    /**
     * @return array
     */
    public function getPermissions();

    /**
     * @param array $permissions permissions
     * @return void
     */
    public function setPermissions($permissions);

    /**
     * Match against permissions, return if matched
     * Permissions are processed based on the 'permissions' config values
     *
     * @param array|\ArrayAccess $user current user array
     * @param \Psr\Http\Message\ServerRequestInterface $request request
     * @return bool true if there is a match in permissions
     */
    public function checkPermissions($user, ServerRequestInterface $request);
}
