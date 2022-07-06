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

namespace CakeDC\Auth\Test\TestCase\Rbac\Rules;

use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use CakeDC\Auth\Rbac\Rules\Owner;
use OutOfBoundsException;
use RuntimeException;

/**
 * @property Owner Owner
 * @property ServerRequest request
 */
class OwnerTest extends TestCase
{
    /**
     * @var \CakeDC\Auth\Rbac\Rules\Owner|mixed
     */
    public $Owner;

    /**
     * @var \Cake\Http\ServerRequest|mixed
     */
    public $request;

    public $fixtures = [
        'plugin.CakeDC/Auth.Posts',
        'plugin.CakeDC/Auth.Users',
        'plugin.CakeDC/Auth.PostsUsers',
    ];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Owner = new Owner();
        $this->request = new ServerRequest();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->Owner);
        parent::tearDown();
    }

    /**
     * test
     *
     * @return void
     */
    public function testAllowedUsingRequestParamsAsDefaults()
    {
        $this->request = $this->request
            ->withParam('plugin', 'CakeDC/Users')
            ->withParam('controller', 'Posts')
            ->withParam('pass', ['00000000-0000-0000-0000-000000000001']);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];

        $this->assertTrue($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * test
     *
     * @return void
     */
    public function testAllowedUsingRequestQuery()
    {
        $this->Owner->setConfig([
                'tableKeyType' => Owner::TYPE_TABLE_KEY_QUERY,
                'tableIdParamsKey' => 'key',
            ]);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->request = $this->request
            ->withParam('plugin', 'CakeDC/Users')
            ->withParam('controller', 'Posts')
            ->withQueryParams([
                'key' => $user['id'],
            ]);

        $this->assertTrue($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * test
     *
     * @return void
     */
    public function testAllowedUsingRequestData()
    {
        $this->Owner->setConfig([
                'tableKeyType' => Owner::TYPE_TABLE_KEY_DATA,
                'tableIdParamsKey' => 'key',
            ]);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->request = $this->request
            ->withParam('plugin', 'CakeDC/Users')
            ->withParam('controller', 'Posts')
            ->withParsedBody([
                'key' => $user['id'],
            ]);

        $this->assertTrue($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * test
     *
     * @return void
     */
    public function testExceptionThrownWhenInvalidTypeTableKey()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('TypeTableKey "computer-says-no" is invalid, please use "params", "data" or "query"');
        $this->Owner->setConfig([
                'tableKeyType' => 'computer-says-no',
                'tableIdParamsKey' => 'key',
            ]);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->request = $this->request
            ->withParam('plugin', 'CakeDC/Users')
            ->withParam('controller', 'Posts')
            ->withParsedBody([
                'key' => $user['id'],
            ]);

        $this->Owner->allowed($user, 'user', $this->request);
    }

    /**
     * test
     *
     * @return void
     */
    public function testAllowedUsingTableAlias()
    {
        $this->Owner = new Owner([
            'table' => 'Posts',
        ]);
        $this->request = $this->request->withParam('pass', ['00000000-0000-0000-0000-000000000001']);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->assertTrue($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * test
     *
     * @return void
     */
    public function testAllowedUsingTableInstance()
    {
        $this->Owner = new Owner([
            'table' => TableRegistry::getTableLocator()->get('CakeDC/Users.Posts'),
        ]);
        $this->request = $this->request->withParam('pass', ['00000000-0000-0000-0000-000000000001']);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->assertTrue($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * test
     *
     * @return void
     */
    public function testAllowedShouldThrowExceptionBecauseEmptyAliasFromRequest()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Missing Table alias, we could not extract a default table from the request');
        $this->request = $this->request->withParam('pass', ['00000000-0000-0000-0000-000000000001']);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->Owner->allowed($user, 'user', $this->request);
    }

    /**
     * test
     *
     * @return void
     */
    public function testAllowedShouldThrowExceptionBecauseForeignKeyNotPresentInTable()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Missing column column_not_found in table Posts while checking ownership permissions for user 00000000-0000-0000-0000-000000000001');
        $this->Owner = new Owner([
            'table' => TableRegistry::getTableLocator()->get('CakeDC/Users.Posts'),
            'ownerForeignKey' => 'column_not_found',
        ]);
        $this->request = $this->request->withParam('pass', ['00000000-0000-0000-0000-000000000001']);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->Owner->allowed($user, 'user', $this->request);
    }

    /**
     * test
     *
     * @return void
     */
    public function testNotAllowedBecauseNotOwner()
    {
        $this->request = $this->request->withAttribute('params', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Posts',
            'pass' => ['00000000-0000-0000-0000-000000000002'],
        ]);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->assertFalse($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * test
     *
     * @return void
     */
    public function testNotAllowedBecauseUserNotFound()
    {
        $this->request = $this->request->withAttribute('params', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Posts',
            'pass' => ['00000000-0000-0000-0000-000000000002'],
        ]);
        $user = [
            'id' => '99999999-0000-0000-0000-000000000000',
        ];
        $this->assertFalse($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * test
     *
     * @return void
     */
    public function testNotAllowedBecausePostNotFound()
    {
        $this->request = $this->request->withAttribute('params', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Posts',
            'pass' => ['99999999-0000-0000-0000-000000000000'], //not found
        ]);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->assertFalse($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * test
     *
     * @return void
     */
    public function testNotAllowedBecauseNoDefaultTable()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Missing column user_id in table NoDefaultTable while checking ownership permissions for user 00000000-0000-0000-0000-000000000001');
        $this->request = $this->request->withAttribute('params', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'NoDefaultTable',
            'pass' => ['00000000-0000-0000-0000-000000000001'],
        ]);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->assertFalse($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * Test using the Owner rule in a belongsToMany association
     * Posts belongsToMany Users
     *
     * @return void
     */
    public function testAllowedBelongsToMany()
    {
        $this->Owner = new Owner([
            'table' => 'PostsUsers',
            'id' => 'post_id',
        ]);
        $this->request = $this->request->withAttribute('params', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'IsNotUsed',
            'pass' => ['00000000-0000-0000-0000-000000000001'],
        ]);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->assertTrue($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * Test using the Owner rule in a belongsToMany association
     * Posts belongsToMany Users
     *
     * @return void
     */
    public function testNotAllowedBelongsToMany()
    {
        $this->Owner = new Owner([
            'table' => 'PostsUsers',
            'id' => 'post_id',
        ]);
        $this->request = $this->request->withAttribute('params', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'IsNotUsed',
            'pass' => ['00000000-0000-0000-0000-000000000002'],
        ]);
        $user = [
            'id' => '00000000-0000-0000-0000-000000000001',
        ];
        $this->assertFalse($this->Owner->allowed($user, 'user', $this->request));
    }

    /**
     * test
     *
     * @return void
     */
    public function testNotAllowedEmptyUserData()
    {
        $this->request = $this->request
            ->withParam('plugin', 'CakeDC/Users')
            ->withParam('controller', 'Posts')
            ->withParam('pass', ['00000000-0000-0000-0000-000000000001']);
        $user = [];
        $this->assertFalse($this->Owner->allowed($user, 'user', $this->request));
    }
}
