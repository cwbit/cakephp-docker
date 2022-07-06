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

use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Log\LogTrait;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use CakeDC\Auth\Rbac\Permissions\AbstractProvider;
use CakeDC\Auth\Rbac\Rules\Rule;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LogLevel;

/**
 * Class Rbac, determine if a request matches any of the given rbac rules
 *
 * @package Rbac
 */
class Rbac implements RbacInterface
{
    use InstanceConfigTrait;
    use LogTrait;

    /**
     * @var array default configuration
     */
    protected $_defaultConfig = [
        // autoload permissions based on a configuration
        'autoload_config' => 'permissions',
        // role field in the Users table
        'role_field' => 'role',
        // default role, used in new users registered and also as role matcher when no role is available
        'default_role' => 'user',
        // Class used to provide the RBAC rules, by default from a config file, must extend AbstractProvider
        'permissions_provider_class' => \CakeDC\Auth\Rbac\Permissions\ConfigProvider::class,
        // Used to set permissions array from configuration, ignoring the permissionsProvider
        'permissions' => null,
        // 'log' will match the value of 'debug' if not set on configuration
        'log' => false,
    ];

    /**
     * A list of rules
     *
     * @var array[] rules array
     */
    protected $permissions;

    /**
     * Rbac constructor.
     *
     * @param array $config Class configuration
     */
    public function __construct($config = [])
    {
        if (!isset($config['log'])) {
            $config['log'] = Configure::read('debug');
        }
        $this->setConfig($config);
        $permissions = $this->getConfig('permissions');
        if ($permissions !== null) {
            $this->permissions = $permissions;
        } else {
            $permissionsProviderClass = $this->getConfig('permissions_provider_class');
            if (!is_subclass_of($permissionsProviderClass, AbstractProvider::class)) {
                throw new \RuntimeException(sprintf('Class "%s" must extend AbstractProvider', $permissionsProviderClass));
            }
            $permissionsProvider = new $permissionsProviderClass([
                'autoload_config' => $this->getConfig('autoload_config'),
            ]);
            $this->permissions = $permissionsProvider->getPermissions();
        }
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param array $permissions permissions
     * @return void
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Match against permissions, return if matched
     * Permissions are processed based on the 'permissions' config values
     *
     * @param array|\ArrayAccess $user current user array
     * @param \Psr\Http\Message\ServerRequestInterface $request request
     * @return bool true if there is a match in permissions
     */
    public function checkPermissions($user, ServerRequestInterface $request)
    {
        $roleField = $this->getConfig('role_field');
        $defaultRole = $this->getConfig('default_role');
        $role = Hash::get($user, $roleField, $defaultRole);

        foreach ($this->permissions as $permission) {
            $matchResult = $this->_matchPermission($permission, $user, $role, $request);
            if ($matchResult !== null) {
                if ($this->getConfig('log')) {
                    $this->log($matchResult->getReason(), LogLevel::DEBUG);
                }

                return $matchResult->isAllowed();
            }
        }

        return false;
    }

    /**
     * Match the rule for current permission
     *
     * @param array<string, mixed> $permission The permission configuration
     * @param array|\ArrayAccess $user Current user data
     * @param string $role Effective user's role
     * @param \Psr\Http\Message\ServerRequestInterface $request Current request
     * @return null|\CakeDC\Auth\Rbac\PermissionMatchResult Null if permission is discarded, PermissionMatchResult if a final
     * result is produced
     */
    protected function _matchPermission(array $permission, $user, $role, ServerRequestInterface $request)
    {
        $issetController = isset($permission['controller']) || isset($permission['*controller']);
        $issetAction = isset($permission['action']) || isset($permission['*action']);
        $issetUser = isset($permission['user']) || isset($permission['*user']);

        if (!$issetController || !$issetAction) {
            $reason = "Cannot evaluate permission when 'controller' and/or 'action' keys are absent";

            return new PermissionMatchResult(false, $reason);
        }
        if ($issetUser) {
            $reason = "Permission key 'user' is illegal, cannot evaluate the permission";

            return new PermissionMatchResult(false, $reason);
        }

        $permission += ['allowed' => true];
        $userArr = ['user' => $user];
        $params = $request->getAttribute('params');
        $reserved = [
            'prefix' => $params['prefix'] ?? null,
            'plugin' => $params['plugin'] ?? null,
            'extension' => $params['_ext'] ?? null,
            'controller' => $params['controller'] ?? null,
            'action' => $params['action'] ?? null,
            'role' => $role,
        ];
        if (!$user && ($permission['bypassAuth'] ?? false) !== true) {
            return null;
        }
        foreach ($permission as $key => $value) {
            $inverse = is_string($key) && $this->_startsWith($key, '*');
            if ($inverse) {
                $key = ltrim($key, '*');
            }

            if (!is_string($value) && is_callable($value)) {
                $return = (bool)call_user_func($value, $user, $role, $request);
            } elseif ($value instanceof Rule) {
                $return = (bool)$value->allowed($user, $role, $request);
            } elseif ($key === 'bypassAuth' && $value === true) {
                $return = true;
            } elseif ($key === 'allowed') {
                $return = !empty($user) && (bool)$value;
            } elseif (array_key_exists($key, $reserved)) {
                $return = $this->_matchOrAsterisk($value, $reserved[$key], true);
            } else {
                if (!$this->_startsWith((string)$key, 'user.')) {
                    $key = 'user.' . $key;
                }

                $return = $this->_matchOrAsterisk($value, Hash::get($userArr, (string)$key));
            }

            if ($inverse) {
                $return = !$return;
            }
            if ($key === 'allowed' || $key === 'bypassAuth') {
                $reason = sprintf(
                    'For %s --> Rule matched %s with result = %s',
                    json_encode($reserved),
                    json_encode($permission),
                    $return
                );

                return new PermissionMatchResult($return, $reason);
            }
            if (!$return) {
                break;
            }
        }

        return null;
    }

    /**
     * Check if rule matched or '*' present in rule matching anything
     *
     * @param string|array|null $possibleValues Values that are accepted (from permission config)
     * @param string|mixed|null $value Value to check with. We'll check the 'dasherized' value too
     * @param bool $allowEmpty If true and $value is null, the rule will pass
     * @return bool
     */
    protected function _matchOrAsterisk($possibleValues, $value, $allowEmpty = false)
    {
        $possibleArray = (array)$possibleValues;

        return $possibleValues === '*' ||
        $value === $possibleValues ||
        in_array($value, $possibleArray) ||
        in_array(Inflector::camelize((string)$value, '-'), $possibleArray);
    }

    /**
     * Checks if $haystack begins with $needle
     *
     * @see http://stackoverflow.com/a/7168986/2588539
     * @param string $haystack The whole string
     * @param string $needle The beginning to check
     * @return bool
     */
    protected function _startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
