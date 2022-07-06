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

namespace CakeDC\Auth\Test\TestCase\Policy;

use Authorization\AuthorizationServiceInterface;
use Authorization\IdentityDecorator;
use Cake\Http\ServerRequestFactory;
use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Policy\RbacPolicy;
use CakeDC\Auth\Rbac\Rbac;

class RbacPolicyTest extends TestCase
{
    /**
     * Test before method, with rbac returning true
     */
    public function testBeforeRbacReturnedTrue()
    {
        $user = new Entity([
            'id' => '00000000-0000-0000-0000-000000000001',
            'password' => '12345',
        ]);
        $service = $this->createMock(AuthorizationServiceInterface::class);
        $identity = new IdentityDecorator($service, $user);
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withAttribute('identity', $identity);
        $rbac = $this->getMockBuilder(Rbac::class)->setMethods(['checkPermissions'])->getMock();
        $request = $request->withAttribute('rbac', $rbac);
        $rbac->expects($this->once())
            ->method('checkPermissions')
            ->with(
                $this->equalTo($identity->getOriginalData()),
                $this->equalTo($request)
            )
            ->will($this->returnValue(true));
        $policy = new RbacPolicy();
        $this->assertTrue($policy->canAccess($identity, $request));
    }

    /**
     * Test before method, with rbac returning false
     */
    public function testBeforeRbacReturnedFalse()
    {
        $user = new Entity([
            'id' => '00000000-0000-0000-0000-000000000001',
            'password' => '12345',
        ]);
        $service = $this->createMock(AuthorizationServiceInterface::class);
        $identity = new IdentityDecorator($service, $user);

        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withAttribute('identity', $identity);
        $rbac = $this->getMockBuilder(Rbac::class)->setMethods(['checkPermissions'])->getMock();
        $request = $request->withAttribute('rbac', $rbac);
        $rbac->expects($this->once())
            ->method('checkPermissions')
            ->with(
                $this->equalTo($identity->getOriginalData()),
                $this->equalTo($request)
            )
            ->will($this->returnValue(false));
        $request = $request->withAttribute('rbac', $rbac);
        $policy = new RbacPolicy();
        $this->assertFalse($policy->canAccess($request->getAttribute('identity'), $request));
    }

    /**
     * Test getRbac method
     */
    public function testGetRbac()
    {
        $request = ServerRequestFactory::fromGlobals();
        $rbac = $this->getMockBuilder(Rbac::class)->setMethods(['checkPermissions'])->getMock();
        $request = $request->withAttribute('rbac', $rbac);
        $policy = new RbacPolicy();
        $actual = $policy->getRbac($request);
        $this->assertSame($rbac, $actual);
    }

    /**
     * Test getRbac method
     */
    public function testGetRbacIgnoreConfigObject()
    {
        $request = ServerRequestFactory::fromGlobals();
        $rbac = $this->getMockBuilder(Rbac::class)->setMethods(['checkPermissions'])->getMock();
        $request = $request->withAttribute('rbac', $rbac);
        $policy = new RbacPolicy([
            'adapter' => new Rbac(['role' => 'my_role']),
        ]);
        $actual = $policy->getRbac($request);
        $this->assertSame($rbac, $actual);
    }

    /**
     * Test getRbac method
     */
    public function testGetRbacUseObject()
    {
        $request = ServerRequestFactory::fromGlobals();
        $rbac = $this->getMockBuilder(Rbac::class)->setMethods(['checkPermissions'])->getMock();
        $policy = new RbacPolicy([
            'adapter' => $rbac,
        ]);
        $actual = $policy->getRbac($request);
        $this->assertSame($rbac, $actual);
    }

    /**
     * Test getRbac method
     */
    public function testGetRbacCreateNew()
    {
        $request = ServerRequestFactory::fromGlobals();
        $policy = new RbacPolicy([
            'adapter' => [
                'autoload_config' => 'my_permissions',
                'role_field' => 'group',
            ],
        ]);
        $rbaResult = $policy->getRbac($request);
        $this->assertInstanceOf(Rbac::class, $rbaResult);
        $expected = [
            'autoload_config' => 'my_permissions',
            'role_field' => 'group',
            'default_role' => 'user',
            'permissions_provider_class' => \CakeDC\Auth\Rbac\Permissions\ConfigProvider::class,
            'permissions' => null,
            'log' => true,
        ];
        $actual = $rbaResult->getConfig();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test getRbac method
     */
    public function testGetRbacCreateSingleton()
    {
        $request = ServerRequestFactory::fromGlobals();
        $policy = $this->getMockBuilder(RbacPolicy::class)
            ->setConstructorArgs([[
                'adapter' => [
                    'autoload_config' => 'my_permissions',
                    'role_field' => 'group',
                ],
            ]])
            ->onlyMethods(['createRbac'])
            ->getMock();
        $policy->expects($this->once())
            ->method('createRbac');
        $rbac1 = $policy->getRbac($request);
        $rbac2 = $policy->getRbac($request);
        $this->assertSame($rbac1, $rbac2);
    }

    /**
     * Test getRbac method
     */
    public function testGetRbacConfigArrayWithoutClassName()
    {
        $request = ServerRequestFactory::fromGlobals();
        $policy = new RbacPolicy([]);
        $policy->setConfig('adapter', [], false);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Config "adapter" should be an object or an array with key className');
        $policy->getRbac($request);
    }
}
