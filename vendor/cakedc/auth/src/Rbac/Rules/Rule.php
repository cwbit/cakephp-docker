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
namespace CakeDC\Auth\Rbac\Rules;

use Psr\Http\Message\ServerRequestInterface;

interface Rule
{
    /**
     * Check the current entity is owned by the logged in user
     *
     * @param array|\ArrayAccess $user Auth array with the logged in data
     * @param string $role role of the user
     * @param \Psr\Http\Message\ServerRequestInterface $request current request, used to get a default table if not provided
     * @return bool
     */
    public function allowed($user, $role, ServerRequestInterface $request);
}
