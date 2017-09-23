<?php

use yii\db\Migration;

class m170923_095529_alter_customer_account_view extends Migration
{
    public function up()
    {
        $this->renameTable('customer_account_info', 'company_account_info');
    }

    public function down()
    {
        echo "m170923_095529_alter_customer_account_view cannot be reverted.\n";

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
