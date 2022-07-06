<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AlterCodeCategoryOnControlsCategories extends AbstractMigration
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
        $table = $this->table('controls_categories');
        $table->changeColumn('code_category', 'string', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();
    }
}
