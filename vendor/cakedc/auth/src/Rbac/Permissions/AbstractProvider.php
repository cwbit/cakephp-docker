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

use Cake\Core\InstanceConfigTrait;
use Cake\Log\LogTrait;

/**
 * Class AbstractProvider, handles getting permission from different sources,
 * for example a config file
 */
abstract class AbstractProvider
{
    use InstanceConfigTrait;
    use LogTrait;

    /**
     * Default permissions to be loaded if no provided permissions
     *
     * @var array
     */
    protected $defaultPermissions;

    /**
     * AbstractProvider constructor.
     *
     * @param array $config config
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
        $this->defaultPermissions = [
            //all bypass
            [
                'prefix' => false,
                'plugin' => 'CakeDC/Users',
                'controller' => 'Users',
                'action' => [
                    // LoginTrait
                    'socialLogin',
                    'login',
                    'logout',
                    'socialEmail',
                    'verify',
                    // RegisterTrait
                    'register',
                    'validateEmail',
                    // PasswordManagementTrait used in RegisterTrait
                    'changePassword',
                    'resetPassword',
                    'requestResetPassword',
                    // UserValidationTrait used in PasswordManagementTrait
                    'resendTokenValidation',
                    'linkSocial',
                ],
                'bypassAuth' => true,
            ],
            [
                'prefix' => false,
                'plugin' => 'CakeDC/Users',
                'controller' => 'SocialAccounts',
                'action' => [
                    'validateAccount',
                    'resendValidation',
                ],
                'bypassAuth' => true,
            ],
            //admin role allowed to all the things
            [
                'role' => 'admin',
                'prefix' => '*',
                'extension' => '*',
                'plugin' => '*',
                'controller' => '*',
                'action' => '*',
            ],
            //specific actions allowed for the all roles in Users plugin
            [
                'role' => '*',
                'plugin' => 'CakeDC/Users',
                'controller' => 'Users',
                'action' => ['profile', 'logout', 'linkSocial', 'callbackLinkSocial'],
            ],
            [
                'role' => '*',
                'plugin' => 'CakeDC/Users',
                'controller' => 'Users',
                'action' => 'resetOneTimePasswordAuthenticator',
                'allowed' => function (array $user, string $role, \Cake\Http\ServerRequest $request): bool {
                    $userId = \Cake\Utility\Hash::get($request->getAttribute('params'), 'pass.0');
                    if (!empty($userId) && !empty($user)) {
                        return $userId === $user['id'];
                    }

                    return false;
                },
            ],
            //all roles allowed to Pages/display
            [
                'role' => '*',
                'controller' => 'Pages',
                'action' => 'display',
            ],
        ];
    }

    /**
     * Provide permissions array, for example
     * [
     *     [
     *          'role' => '*',
     *          'plugin' => null,
     *          'controller' => ['Pages'],
     *          'action' => ['display'],
     *      ],
     * ]
     *
     * @return array Array of permissions
     */
    abstract public function getPermissions();

    /**
     * @return array
     */
    public function getDefaultPermissions()
    {
        return $this->defaultPermissions;
    }

    /**
     * @param array $defaultPermissions default permissions
     * @return void
     */
    public function setDefaultPermissions($defaultPermissions)
    {
        $this->defaultPermissions = $defaultPermissions;
    }
}
