<?php

use yii\db\Migration;

class m170714_101057_alter_invoice_discount extends Migration
{
    public function up()
    {
        $this->alterColumn('invoice_discount', 'value', $this->decimal(10, 2));
    }

    public function down()
    {
        echo "m170714_101057_alter_invoice_discount cannot be reverted.\n";

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
