<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AlterUserIdOnUserSectors extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('user_sectors');
        $table->changeColumn('user_id', 'uuid', [
            'null' => false,
        ]);
        $table->update();
    }
}
