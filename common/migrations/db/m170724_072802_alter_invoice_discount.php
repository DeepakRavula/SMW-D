<?php

use yii\db\Migration;

class m170724_072802_alter_invoice_discount extends Migration
{
    public function up()
    {
        $this->renameTable('invoice_discount', 'invoice_line_item_discount');
        $this->renameColumn('invoice_line_item_discount', 'invoiceId', 'invoiceLineItemId');
    }

    public function down()
    {
        echo "m170724_072802_alter_invoice_discount cannot be reverted.\n";

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
