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
namespace CakeDC\Auth\Authentication;

use Cake\Core\Configure;

/**
 * Factory for two authentication checker
 *
 * @package CakeDC\Auth\Authentication
 */
class OneTimePasswordAuthenticationCheckerFactory
{
    /**
     * Get the two factor authentication checker
     *
     * @return \CakeDC\Auth\Authentication\OneTimePasswordAuthenticationCheckerInterface
     */
    public function build()
    {
        $className = Configure::read('OneTimePasswordAuthenticator.checker');
        $interfaces = class_implements($className);
        $required = OneTimePasswordAuthenticationCheckerInterface::class;

        if (in_array($required, $interfaces)) {
            return new $className();
        }
        throw new \InvalidArgumentException("Invalid config for 'OneTimePasswordAuthenticator.checker', '$className' does not implement '$required'");
    }
}
