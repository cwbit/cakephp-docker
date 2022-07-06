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

namespace CakeDC\Auth\Rbac\Permissions;

use Cake\Core\Configure;
use Psr\Log\LogLevel;

/**
 * Class ConfigProvider, handles permission loading from configuration file
 *
 * @package Rbac
 */
class ConfigProvider extends AbstractProvider
{
    /**
     * @var array default configuration
     */
    protected $_defaultConfig = [
        'autoload_config' => 'permissions',
    ];

    /**
     * Provide permissions array
     *
     * @return array Array of permissions
     */
    public function getPermissions()
    {
        $autoload = $this->getConfig('autoload_config');

        if ($autoload) {
            if (is_array($autoload)) {
                $permissions = [];

                foreach ($autoload as $configItem) {
                    $permissions = array_merge($permissions, $this->_loadPermissions($configItem));
                }

                return $permissions;
            }

            return $this->_loadPermissions($autoload);
        }

        return $this->defaultPermissions;
    }

    /**
     * Load config and retrieve permissions
     * If the configuration file does not exist, or the permissions key not present, return defaultPermissions
     * To be mocked
     *
     * @param string $key name of the configuration file to read permissions from
     * @return array permissions
     */
    protected function _loadPermissions($key)
    {
        $permissions = null;
        try {
            Configure::load($key, 'default');
            $permissions = Configure::read('CakeDC/Auth.permissions');
            $legacyPermissions = Configure::read('Users.SimpleRbac.permissions');
            if (!$permissions && $legacyPermissions) {
                $permissions = $legacyPermissions;
            }
        } catch (\Exception $ex) {
            $msg = sprintf('Missing configuration file: "config/%s.php". Using default permissions', $key);
            $this->log($msg, LogLevel::WARNING);
        }

        if (empty($permissions)) {
            return $this->defaultPermissions;
        }

        return $permissions;
    }
}
