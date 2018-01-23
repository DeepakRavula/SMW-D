<?php

use yii\db\Migration;

class m171110_164449_line_item_discount_decimal_places extends Migration
{
    public function up()
    {
        $this->alterColumn('invoice_line_item_discount', 'value', $this->decimal(10, 4));
    }

    public function down()
    {
        echo "m171110_164449_line_item_discount_decimal_places cannot be reverted.\n";

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
