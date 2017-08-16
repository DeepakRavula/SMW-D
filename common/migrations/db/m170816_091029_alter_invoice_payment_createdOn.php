<?php

use yii\db\Migration;

class m170816_091029_alter_invoice_payment_createdOn extends Migration
{
    public function up()
    {
        $this->alterColumn('invoice', 'createdOn', 'varchar(50) NULL');
    }

    public function down()
    {
        echo "m170816_091029_alter_invoice_payment_createdOn cannot be reverted.\n";

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
