<?php

use yii\db\Migration;

class m171107_080126_invoice_decimal_place extends Migration
{
    public function up()
    {
        $this->alterColumn('invoice_line_item', 'tax_rate', $this->decimal(10, 4));
        $this->alterColumn('invoice', 'subTotal', $this->decimal(10, 4));
        $this->alterColumn('invoice', 'tax', $this->decimal(10, 4));
        $this->alterColumn('invoice', 'total', $this->decimal(10, 4));
        $this->alterColumn('invoice', 'balance', $this->decimal(10, 4));
    }

    public function down()
    {
        echo "m171107_080126_invoice_decimal_place cannot be reverted.\n";

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
