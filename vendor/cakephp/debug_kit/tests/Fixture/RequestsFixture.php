<?php
/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Requests fixture.
 *
 * Used to create schema for tests and at runtime.
 */
class RequestsFixture extends TestFixture
{
    /**
     * table property
     *
     * This is necessary to prevent userland inflections from causing issues.
     *
     * @var string
     */
    public $table = 'requests';

    /**
     * fields property
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'uuid', 'null' => false],
        'url' => ['type' => 'text', 'null' => false],
        'content_type' => ['type' => 'string'],
        'status_code' => ['type' => 'integer'],
        'method' => ['type' => 'string'],
        'requested_at' => ['type' => 'datetime', 'null' => false],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

    /**
     * Constructor
     *
     * @param string $connection The connection name to use.
     */
    public function __construct($connection = null)
    {
        if ($connection) {
            $this->connection = $connection;
        }
        $this->init();
    }
}
