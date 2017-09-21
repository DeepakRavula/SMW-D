<?php

use yii\db\Migration;

class m170921_093157_drop_lesson_reschedule_and_lesson_split extends Migration
{
    public function up()
    {
        $this->dropTable('lesson_split');
        $this->dropTable('lesson_reschedule');
    }

    public function down()
    {
        echo "m170921_093157_drop_lesson_reschedule_and_lesson_split cannot be reverted.\n";

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
