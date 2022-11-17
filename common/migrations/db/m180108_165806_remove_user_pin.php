<?php

use yii\db\Migration;

class m180108_165806_remove_user_pin extends Migration
{
    public function up()
    {
        $this->dropTable('user_pin');
    }

    public function down()
    {
        echo "m180108_165806_remove_user_pin cannot be reverted.\n";

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
