<?php
declare(strict_types=1);

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2020 Juan Pablo Ramirez and Nicolas Masson
 * @link          https://webrider.de/
 * @since         1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Datasource\ConnectionManager;
use Migrations\AbstractMigration;

class InitialMigration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        // Sqlite is not happy with the composite and/or uuid concept
        if (ConnectionManager::getConfig('test')['driver'] === 'Cake\Database\Driver\Sqlite') {
            $citiesTable = $this->table('cities');
        } else {
            $citiesTable = $this->table('cities', ['id' => false, 'primary_key' => ['uuid_primary_key', 'id_primary_key']])
                ->addColumn('uuid_primary_key', 'uuid', [
                    'default' => \CakephpTestSuiteLight\Test\TestUtil::makeUuid(),
                ])
                ->addColumn('id_primary_key', 'integer', [
                    'default' => rand(1, 999999999),
                ]);
        }

        $citiesTable
            ->addColumn('name', 'string', [
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('country_id', 'integer', [
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex('country_id')
            ->addTimestamps('created', 'modified')
            ->create();

        $this->table('countries')
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'limit' => 128,
                'null' => false,
            ])
            ->addTimestamps('created', 'modified')
            ->create();

        $this->table('cities')
            ->addForeignKey('country_id', 'countries', 'id', ['delete'=>'RESTRICT', 'update'=>'CASCADE'])
            ->save();

        // Veeeery long table name (= 59) to ensure that the triggers get correctly created.
        $this->table('123456789_123456789_123456789_123456789_123456789_123456789')->create();
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        $this->table('cities')->drop();
        $this->table('countries')->drop();
    }
}
