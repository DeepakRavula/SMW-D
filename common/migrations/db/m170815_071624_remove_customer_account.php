<?php

use yii\db\Migration;

class m170815_071624_remove_customer_account extends Migration
{
    public function up()
    {
        $this->dropTable('customer_account');
    }

    public function down()
    {
        echo "m170815_071624_remove_customer_account cannot be reverted.\n";

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
