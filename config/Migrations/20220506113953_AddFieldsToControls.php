<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddFieldsToControls extends AbstractMigration
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
        $table = $this->table('controls');
        $table->addColumn('date_time', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('checklist_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();
    }
}
