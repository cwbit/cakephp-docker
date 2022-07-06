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
use CakeDC\Auth\Policy\SuperuserPolicy;

class SuperuserPolicyTest extends TestCase
{
    /**
     * Data provider for testCanAccess
     *
     * @return array
     */
    public function dataProviderCanAccess()
    {
        return [
            [['is_superuser' => true], true],
            [['is_superuser' => false], false],
            [[], false],
            [['is_superuser' => true], false, ['superuser_field' => 'is_master']],
            [['is_master' => true], true, ['superuser_field' => 'is_master']],
            [['is_master' => false], false, ['superuser_field' => 'is_master']],
            [['is_superuser' => false], false, ['superuser_field' => 'is_master']],
            [[], false, ['superuser_field' => 'is_master']],
        ];
    }

    /**
     * Test canAccess method
     *
     * @param array $userData custom user data for testing.
     * @param bool $expected The expected result.
     * @param array $config policy configurations.
     * @dataProvider dataProviderCanAccess
     * @return void
     */
    public function testCanAccess($userData, $expected, $config = [])
    {
        $user = new Entity($userData + [
            'id' => '00000000-0000-0000-0000-000000000001',
        ]);
        $service = $this->createMock(AuthorizationServiceInterface::class);
        $identity = new IdentityDecorator($service, $user);
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withAttribute('identity', $identity);

        $policy = new SuperuserPolicy($config);
        $actual = $policy->canAccess($request->getAttribute('identity'), $request);
        $this->assertSame($expected, $actual);
    }
}
