<?php

use yii\db\Migration;

/**
 * Class m210708_113602_change_balance_column
 */
class m210708_113602_change_balance_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('customer_account', 'balance', 
        $this->decimal(15, 4)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210708_113602_change_balance_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210708_113602_change_balance_column cannot be reverted.\n";

        return false;
    }
    */
}
