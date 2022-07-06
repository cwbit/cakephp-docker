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

namespace CakeDC\Auth\Test\TestCase\Rbac;

use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Rbac\Rbac;
use CakeDC\Auth\Rbac\Rules\Owner;
use CakeDC\Auth\Test\App\Auth\Rule\SampleRule;
use Psr\Log\LogLevel;
use ReflectionClass;
use RuntimeException;

class RbacTest extends TestCase
{
    public $simpleRbacAuthorize;
    public $registry;
    /**
     * @var Rbac
     */
    protected $rbac;

    protected $defaultPermissions;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $request = new ServerRequest();
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
                'allowed' => true,
            ],
            //all roles allowed to Pages/display
            [
                'role' => '*',
                'controller' => 'Pages',
                'action' => 'display',
            ],
        ];
        $this->rbac = new Rbac(null, $this->defaultPermissions);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->rbac);
    }

    /**
     * @covers \CakeDC\Auth\Rbac\Rbac::__construct
     */
    public function testConstructGetDefaultPermissions()
    {
        $this->rbac = new Rbac();
        $result = $this->rbac->getPermissions();
        $this->assertTrue(is_callable($result[4]['allowed']));
        $result[4]['allowed'] = true;
        $this->assertSame($this->defaultPermissions, $result);
    }

    /**
     * @covers \CakeDC\Auth\Rbac\Rbac::__construct
     */
    public function testConstructBadProvider()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Class "\Exception" must extend AbstractProvider');
        $this->rbac = new Rbac([
            'permissions_provider_class' => '\Exception',
        ]);
    }

    /**
     * @covers \CakeDC\Auth\Rbac\Rbac::__construct
     */
    public function testConstructSetPermissions()
    {
        $this->rbac = new Rbac([
            'permissions' => [],
        ]);
        $this->assertEmpty($this->rbac->getPermissions());
    }

    protected function assertConstructorPermissions($instance, $config, $permissions)
    {
        $reflectedClass = new ReflectionClass($instance);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($this->simpleRbacAuthorize, $this->registry, $config);

        //we should have the default permissions
        $resultPermissions = $this->simpleRbacAuthorize->getConfig('permissions');
        $this->assertEquals($permissions, $resultPermissions);
    }

    /**
     * @dataProvider providerAuthorize
     */
    public function testAuthorize($permissions, $user, $requestParams, $expected)
    {
        $this->rbac = new Rbac(['permissions' => $permissions]);
        $request = $this->_requestFromArray($requestParams);

        $result = $this->rbac->checkPermissions($user, $request);
        $this->assertSame($expected, $result);
    }

    public function providerAuthorize()
    {
        $trueRuleMock = $this->getMockBuilder(Owner::class)
            ->setMethods(['allowed'])
            ->getMock();
        $trueRuleMock->expects($this->any())
            ->method('allowed')
            ->willReturn(true);

        return [
            'discard-first' => [
                //permissions
                [
                    [
                        'role' => 'test',
                        'controller' => 'Tests',
                        'action' => 'three', // Discard here
                        function () {
                            throw new \Exception();
                        },
                    ],
                    [
                        'plugin' => ['Tests'],
                        'role' => ['test'],
                        'controller' => ['Tests'],
                        'action' => ['one', 'two'],
                    ],
                ],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'deny-first-discard-after' => [
                //permissions
                [
                    [
                        'role' => 'test',
                        'controller' => 'Tests',
                        'action' => 'one',
                        'allowed' => function () {
                            return false; // Deny here since under 'allowed' key
                        },
                    ],
                    [
                        // This permission isn't evaluated
                        function () {
                            throw new \Exception();
                        },
                        'plugin' => ['Tests'],
                        'role' => ['test'],
                        'controller' => ['Tests'],
                        'action' => ['one', 'two'],
                    ],
                ],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                false,
            ],
            'star-invert' => [
                //permissions
                [[
                    '*plugin' => 'Tests',
                    '*role' => 'test',
                    '*controller' => 'Tests',
                    '*action' => 'test',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'something',
                ],
                //request
                [
                    'plugin' => 'something',
                    'controller' => 'something',
                    'action' => 'something',
                ],
                //expected
                true,
            ],
            'star-invert-deny' => [
                //permissions
                [[
                    '*plugin' => 'Tests',
                    '*role' => 'test',
                    '*controller' => 'Tests',
                    '*action' => 'test',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'something',
                ],
                //request
                [
                    'plugin' => 'something',
                    'controller' => 'something',
                    'action' => 'test',
                ],
                //expected
                false,
            ],
            'user-arr' => [
                //permissions
                [
                    [
                        'username' => 'luke',
                        'user.id' => 1,
                        'profile.id' => 256,
                        'user.profile.signature' => "Hi I'm luke",
                        'user.allowed' => false,
                        'controller' => 'Tests',
                        'action' => 'one',
                    ],
                ],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                    'profile' => [
                        'id' => 256,
                        'signature' => "Hi I'm luke",
                    ],
                    'allowed' => false,
                ],
                //request
                [
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'evaluate-order' => [
                //permissions
                [
                    [
                        'allowed' => false,
                        function () {
                            throw new \Exception();
                        },
                        'controller' => 'Tests',
                        'action' => 'one',
                    ],
                ],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                false,
            ],
            'multiple-callables' => [
                //permissions
                [
                    [
                        function () {
                            return true;
                        },
                        clone $trueRuleMock,
                        function () {
                            return true;
                        },
                        'controller' => 'Tests',
                        'action' => 'one',
                    ],
                ],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'happy-strict-all' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'role' => 'test',
                    'controller' => 'Tests',
                    'action' => 'test',
                    'allowed' => true,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                true,
            ],
            'happy-strict-all-deny' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'role' => 'test',
                    'controller' => 'Tests',
                    'action' => 'test',
                    'allowed' => false,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                false,
            ],
            'happy-plugin-null-allowed-null' => [
                //permissions
                [[
                    'role' => 'test',
                    'controller' => 'Tests',
                    'action' => 'test',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => null,
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                true,
            ],
            'happy-plugin-asterisk' => [
                //permissions
                [[
                    'plugin' => '*',
                    'role' => 'test',
                    'controller' => 'Tests',
                    'action' => 'test',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Any',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                true,
            ],
            'happy-plugin-asterisk-main-app' => [
                //permissions
                [[
                    'plugin' => '*',
                    'role' => 'test',
                    'controller' => 'Tests',
                    'action' => 'test',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => null,
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                true,
            ],
            'happy-role-asterisk' => [
                //permissions
                [[
                    'role' => '*',
                    'controller' => 'Tests',
                    'action' => 'test',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'any-role',
                ],
                //request
                [
                    'plugin' => null,
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                true,
            ],
            'happy-controller-asterisk' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'role' => 'test',
                    'controller' => '*',
                    'action' => 'test',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                true,
            ],
            'happy-action-asterisk' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'role' => 'test',
                    'controller' => 'Tests',
                    'action' => '*',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'any',
                ],
                //expected
                true,
            ],
            'happy-some-asterisk-allowed' => [
                //permissions
                [[
                    'plugin' => '*',
                    'role' => 'test',
                    'controller' => '*',
                    'action' => '*',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'any',
                ],
                //expected
                true,
            ],
            'happy-some-asterisk-deny' => [
                //permissions
                [[
                    'plugin' => '*',
                    'role' => 'test',
                    'controller' => '*',
                    'action' => '*',
                    'allowed' => false,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'any',
                ],
                //expected
                false,
            ],
            'all-deny' => [
                //permissions
                [[
                    'plugin' => '*',
                    'role' => '*',
                    'controller' => '*',
                    'action' => '*',
                    'allowed' => false,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Any',
                    'controller' => 'Any',
                    'action' => 'any',
                ],
                //expected
                false,
            ],
            'dasherized' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'role' => 'test',
                    'controller' => 'TestTests',
                    'action' => 'TestAction',
                    'allowed' => true,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'tests',
                    'controller' => 'test-tests',
                    'action' => 'test-action',
                ],
                //expected
                true,
            ],
            'happy-array' => [
                //permissions
                [[
                    'plugin' => ['Tests'],
                    'role' => ['test'],
                    'controller' => ['Tests'],
                    'action' => ['one', 'two'],
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'happy-array-deny' => [
                //permissions
                [[
                    'plugin' => ['Tests'],
                    'role' => ['test'],
                    'controller' => ['Tests'],
                    'action' => ['one', 'two'],
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'three',
                ],
                //expected
                false,
            ],
            'happy-callback-check-params' => [
                //permissions
                [[
                    'plugin' => ['Tests'],
                    'role' => ['test'],
                    'controller' => ['Tests'],
                    'action' => ['one', 'two'],
                    'allowed' => function ($user, $role, $request) {
                        return $user['id'] === 1 && $role = 'test' && $request->getParam('plugin') == 'Tests';
                    },
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'happy-callback-deny' => [
                //permissions
                [[
                    'plugin' => ['*'],
                    'role' => ['test'],
                    'controller' => ['Tests'],
                    'action' => ['one', 'two'],
                    'allowed' => function ($user, $role, $request) {
                        return false;
                    },
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                false,
            ],
            'happy-prefix' => [
                //permissions
                [[
                    'role' => ['test'],
                    'prefix' => ['admin'],
                    'controller' => ['Tests'],
                    'action' => ['one', 'two'],
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'prefix' => 'admin',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'deny-prefix' => [
                //permissions
                [[
                    'role' => ['test'],
                    'prefix' => ['admin'],
                    'controller' => ['Tests'],
                    'action' => ['one', 'two'],
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                false,
            ],
            'star-prefix' => [
                //permissions
                [[
                    'role' => ['test'],
                    'prefix' => '*',
                    'controller' => ['Tests'],
                    'action' => ['one', 'two'],
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'prefix' => 'admin',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'array-prefix' => [
                //permissions
                [[
                    'role' => ['test'],
                    'prefix' => ['one', 'admin'],
                    'controller' => '*',
                    'action' => '*',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'prefix' => 'admin',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'array-prefix-deny' => [
                //permissions
                [[
                    'role' => ['test'],
                    'prefix' => ['one', 'admin'],
                    'controller' => '*',
                    'action' => 'one',
                    'allowed' => false,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'prefix' => 'admin',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                false,
            ],
            'happy-ext' => [
                //permissions
                [[
                    'role' => ['test'],
                    'prefix' => ['admin'],
                    'extension' => ['csv'],
                    'controller' => ['Tests'],
                    'action' => ['one', 'two'],
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'prefix' => 'admin',
                    '_ext' => 'csv',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'deny-ext' => [
                //permissions
                [[
                    'role' => ['test'],
                    'extension' => ['csv'],
                    'controller' => ['Tests'],
                    'action' => ['one', 'two'],
                    'allowed' => false,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'controller' => 'Tests',
                    '_ext' => 'csv',
                    'action' => 'one',
                ],
                //expected
                false,
            ],
            'star-ext' => [
                //permissions
                [[
                    'role' => ['test'],
                    'prefix' => '*',
                    'extension' => '*',
                    'controller' => ['Tests'],
                    'action' => ['one', 'two'],
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'prefix' => 'admin',
                    '_ext' => 'other',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'array-ext' => [
                //permissions
                [[
                    'role' => ['test'],
                    'extension' => ['csv', 'pdf'],
                    'controller' => '*',
                    'action' => '*',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    '_ext' => 'csv',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'array-ext-deny' => [
                //permissions
                [[
                    'role' => ['test'],
                    'extension' => ['csv', 'docx'],
                    'controller' => '*',
                    'action' => 'one',
                    'allowed' => false,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'prefix' => 'csv',
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                false,
            ],
            'rule-class' => [
                //permissions
                [
                    [
                        'role' => ['test'],
                        'controller' => '*',
                        'action' => 'one',
                        'allowed' => $trueRuleMock,
                    ],
                ],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'controller' => 'Tests',
                    'action' => 'one',
                ],
                //expected
                true,
            ],
            'bypass-auth' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                    'bypassAuth' => true,
                ]],
                //user
                [],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                true,
            ],
            'bypass-auth-callable' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                    'bypassAuth' => false,
                ]],
                //user
                [],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                false,
            ],
            'bypass-auth-rule-not-allowed-order-matters' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                    'role' => '*',
                    'bypassAuth' => true,
                    'allowed' => false,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                true,
            ],
            'rule-not-allowed-bypass-auth-order-matters' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                    'role' => '*',
                    'allowed' => false,
                    'bypassAuth' => true,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                false,
            ],
            'bypass-auth-user-not-authorized-another-role' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                    'role' => 'admin',
                    'bypassAuth' => true,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                false,
            ],
            'custom-rule-any-role' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                    'role' => 'admin',
                    'allowed' => new SampleRule(),
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'admin',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                true,
            ],
            'custom-rule-no-role' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                    'role' => 'admin',
                    'allowed' => new SampleRule(),
                ]],
                //user
                [
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                false,
            ],
        ];
    }

    /**
     * @dataProvider badPermissionProvider
     * @param array $permissions
     * @param array $user
     * @param array $requestParams
     * @param string $expectedMsg
     */
    public function testBadPermission($permissions, $user, $requestParams, $expectedMsg)
    {
        $rbac = $this->getMockBuilder(Rbac::class)
            ->setMethods(['log'])
            ->disableOriginalConstructor()
            ->getMock();
        $rbac
            ->expects($this->once())
            ->method('log')
            ->with($expectedMsg, LogLevel::DEBUG);

        $rbac->setConfig('log', true);
        $rbac->setPermissions($permissions);
        $request = $this->_requestFromArray($requestParams);

        $rbac->checkPermissions($user, $request);
    }

    public function badPermissionProvider()
    {
        return [
            'no-controller' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'role' => 'test',
                    //'controller' => 'Tests',
                    'action' => 'test',
                    'allowed' => true,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                "Cannot evaluate permission when 'controller' and/or 'action' keys are absent",
            ],
            'no-action' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'role' => 'test',
                    'controller' => 'Tests',
                    //'action' => 'test',
                    'allowed' => true,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                "Cannot evaluate permission when 'controller' and/or 'action' keys are absent",
            ],
            'no-controller-and-action' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'role' => 'test',
                    //'controller' => 'Tests',
                    //'action' => 'test',
                    'allowed' => true,
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                "Cannot evaluate permission when 'controller' and/or 'action' keys are absent",
            ],
            'no-controller and user-key' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'role' => 'test',
                    //'controller' => 'Tests',
                    'action' => 'test',
                    'allowed' => true,
                    'user' => 'something',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                "Cannot evaluate permission when 'controller' and/or 'action' keys are absent",
            ],
            'user-key' => [
                //permissions
                [[
                    'plugin' => 'Tests',
                    'role' => 'test',
                    'controller' => 'Tests',
                    'action' => 'test',
                    'allowed' => true,
                    'user' => 'something',
                ]],
                //user
                [
                    'id' => 1,
                    'username' => 'luke',
                    'role' => 'test',
                ],
                //request
                [
                    'plugin' => 'Tests',
                    'controller' => 'Tests',
                    'action' => 'test',
                ],
                //expected
                "Permission key 'user' is illegal, cannot evaluate the permission",
            ],
        ];
    }

    /**
     * @param array $params
     * @return ServerRequest
     */
    protected function _requestFromArray($params)
    {
        $request = new ServerRequest();

        return $request
            ->withParam('plugin', $params['plugin'] ?? null)
            ->withParam('controller', $params['controller'] ?? null)
            ->withParam('action', $params['action'] ?? null)
            ->withParam('prefix', $params['prefix'] ?? null)
            ->withParam('_ext', $params['_ext'] ?? null);
    }

    public function testGetPermissions()
    {
        $permissions = ['test'];
        $this->rbac->setPermissions($permissions);
        $this->assertSame($permissions, $this->rbac->getPermissions());
    }
}
