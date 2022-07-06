<?php
declare(strict_types=1);

namespace App\Model\Table;

use CakeDC\Users\Model\Table\UsersTable;

/**
 * Application specific Users Table with non plugin conform field(s)
 */
class CustomUsersTable extends UsersTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        //$this->hasOne('CompanyUsers', ['foreignKey' => 'user_id']);

        $this->hasMany('Checklists', [
            'foreignKey' => 'author_id',
        ]);

        $this->hasMany('UserSectors', [
            'foreignKey' => 'user_id',
        ])
        ->setSaveStrategy('replace');
    }
}
