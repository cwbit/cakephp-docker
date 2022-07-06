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

namespace CakeDC\Auth\Test\TestCase\Rbac\Permissions;

use Cake\TestSuite\TestCase;
use CakeDC\Auth\Rbac\Permissions\ConfigProvider;

/**
 * ConfigProviderTest
 *
 * @property ConfigProvider configProvider
 */
class ConfigProviderTest extends TestCase
{
    /**
     * @var \CakeDC\Auth\Rbac\Permissions\ConfigProvider|mixed
     */
    public $configProvider;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->configProvider = new ConfigProvider();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->configProvider);
    }

    /**
     * test
     *
     * @return void
     */
    public function testGetPermissionsNoAutoload()
    {
        $this->configProvider->setConfig('autoload_config', null);
        $this->configProvider->setDefaultPermissions([
            [
                'controller' => 'Posts',
                'action' => 'default',
            ],
        ]);
        $permissions = $this->configProvider->getPermissions();
        $this->assertSame($this->configProvider->getDefaultPermissions(), $permissions);
    }

    /**
     * test
     *
     * @return void
     */
    public function testGetPermissionsAutoloadMissingFileShouldReturnDefaultPermissions()
    {
        $this->configProvider->setConfig('autoload_config', 'missingFile');
        $permissions = $this->configProvider->getPermissions();
        $this->assertSame($this->configProvider->getDefaultPermissions(), $permissions);
    }

    /**
     * test
     *
     * @return void
     */
    public function testGetPermissionsAutoload()
    {
        $this->configProvider->setConfig('autoload_config', 'existing');
        $permissions = $this->configProvider->getPermissions();
        $this->assertSame([
            'controller' => 'Posts',
            'action' => 'display',
        ], $permissions);
    }
}
