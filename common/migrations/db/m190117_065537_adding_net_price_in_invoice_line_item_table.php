<?php

use yii\db\Migration;

/**
 * Class m190117_065537_adding_net_price_in_invoice_line_item_table
 */
class m190117_065537_adding_net_price_in_invoice_line_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('invoice_line_item', 'netTotal', $this->decimal(10, 4)->notNull()->defaultValue(0.00));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190117_065537_adding_net_price_in_invoice_line_item_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190117_065537_adding_net_price_in_invoice_line_item_table cannot be reverted.\n";

        return false;
    }
    */
}
