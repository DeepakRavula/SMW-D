<?php

use yii\db\Migration;

class m170728_044034_alter_invoice_line_item_cost extends Migration
{
    public function up()
    {
        $this->alterColumn('invoice_line_item', 'cost', $this->decimal(10, 2));
    }

    public function down()
    {
        echo "m170728_044034_alter_invoice_line_item_cost cannot be reverted.\n";

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
