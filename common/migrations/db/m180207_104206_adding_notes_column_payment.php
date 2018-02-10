<?php

use yii\db\Migration;

class m180207_104206_adding_notes_column_payment extends Migration
{
    public function up()
    {
         $this->addColumn('payment', 'notes', 'TEXT  AFTER reference');

    }

    public function down()
    {
        echo "m180207_104206_adding_notes_column_payment cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
