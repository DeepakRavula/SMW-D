<?php

use yii\db\Migration;

class m170928_105425_drop_phone_label extends Migration
{
    public function up()
    {
        $this->dropTable('phone_label');
    }

    public function down()
    {
        echo "m170928_105425_drop_phone_label cannot be reverted.\n";

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
