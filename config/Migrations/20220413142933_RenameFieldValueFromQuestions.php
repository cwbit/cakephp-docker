<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RenameFieldValueFromQuestions extends AbstractMigration
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
        $table = $this->table('questions');
        $table->renameColumn('value', 'question_value');
        $table->renameColumn('leader', 'leader_alert');
        $table->update();
    }
}
