<?php

use yii\db\Migration;

class m170813_130027_alter_lesson_closure extends Migration
{
    public function up()
    {
        $this->dropColumn('lesson_hierarchy', 'id');
        $this->dropTable('invoice_item_payment_cycle_lesson_split');
    }

    public function down()
    {
        echo "m170813_130027_alter_lesson_closure cannot be reverted.\n";

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
