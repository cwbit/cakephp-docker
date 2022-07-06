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
namespace CakeDC\Auth\Test\TestCase\Traits;

use Authentication\Identity;
use Authorization\AuthorizationService;
use Authorization\IdentityDecorator;
use Authorization\Policy\MapResolver;
use Authorization\Policy\OrmResolver;
use Authorization\Policy\ResolverCollection;
use Cake\Http\ServerRequest;
use Cake\ORM\Entity;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Policy\CollectionPolicy;
use CakeDC\Auth\Policy\RbacPolicy;
use CakeDC\Auth\Policy\SuperuserPolicy;
use CakeDC\Auth\Rbac\Rbac;

/**
 * Class IsAuthorizedTraitTest
 *
 * @package CakeDC\Auth\Test\TestCase\Traits
 */
class IsAuthorizedTraitTest extends TestCase
{
    /**
     * Data provider for testIsAuthorized
     *
     * @return array
     */
    public function dataProviderIsAuthorized()
    {
        $url = [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'myTest',
        ];

        return [
            [$url, true],
            [$url, false],
            ['/my-test', true],
            ['/my-test', false],
        ];
    }

    /**
     * Test isAuthorized empty url
     *
     * @return void
     */
    public function testIsAuthorizedEmpty()
    {
        $Trait = $this->getMockBuilder(\CakeDC\Auth\Traits\IsAuthorizedTrait::class)
            ->setMethods(['getRequest'])
            ->getMockForTrait();
        $Trait->expects($this->never())
            ->method('getRequest');
        $this->assertFalse($Trait->isAuthorized(null));
        $this->assertFalse($Trait->isAuthorized([]));
        $this->assertFalse($Trait->isAuthorized(''));
    }

    /**
     * Test isAuthorized
     *
     * @param mixed $url The url to test.
     * @param bool $authorize Is authorized?
     * @dataProvider dataProviderIsAuthorized
     * @return void
     */
    public function testIsAuthorizedWithMock($url, $authorize, $invalidUrl = false)
    {
        $builder = Router::createRouteBuilder('/');
        $builder->connect('/my-test', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'myTest',
        ]);
        $user = new Entity([
            'id' => '00000000-0000-0000-0000-000000000001',
            'password' => '12345',
        ]);
        $identity = new Identity($user);
        $request = new ServerRequest();
        $rbac = $this->getMockBuilder(Rbac::class)->setMethods(['checkPermissions'])->getMock();
        $rbac->expects($this->once())
            ->method('checkPermissions')
            ->with(
                $this->equalTo($identity->getOriginalData())
            )
            ->will($this->returnValue($authorize));
        $request = $request->withAttribute('rbac', $rbac);

        $map = new MapResolver();
        $map->map(
            ServerRequest::class,
            new CollectionPolicy([
                SuperuserPolicy::class,
                RbacPolicy::class,
            ])
        );
        $orm = new OrmResolver();
        $resolver = new ResolverCollection([
            $map,
            $orm,
        ]);
        $service = new AuthorizationService($resolver);
        $request = $request->withAttribute('authorization', $service);
        $request = $request->withAttribute('identity', new IdentityDecorator($service, $identity));

        $Trait = $this->getMockBuilder(\CakeDC\Auth\Traits\IsAuthorizedTrait::class)
            ->setMethods(['getRequest'])
            ->getMockForTrait();
        $Trait->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $result = $Trait->isAuthorized($url);
        $this->assertSame($authorize, $result);
    }

    /**
     * Test isAuthorized without authorization service
     *
     * @return void
     */
    public function testIsAuthorizedWithoutService()
    {
        $builder = Router::createRouteBuilder('/');
        $builder->connect('/my-test', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'myTest',
        ]);
        $request = new ServerRequest();
        $rbac = $this->getMockBuilder(Rbac::class)->setMethods(['checkPermissions'])->getMock();
        $rbac->expects($this->never())
            ->method('checkPermissions');
        $request = $request->withAttribute('rbac', $rbac);

        $Trait = $this->getMockBuilder(\CakeDC\Auth\Traits\IsAuthorizedTrait::class)
            ->setMethods(['getRequest'])
            ->getMockForTrait();
        $Trait->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not find the authorization service in the request.');
        $Trait->isAuthorized([
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'myTest',
        ]);
    }
}
