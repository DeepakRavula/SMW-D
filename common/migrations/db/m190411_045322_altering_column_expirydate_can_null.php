<?php

use yii\db\Migration;

/**
 * Class m190411_045322_altering_column_expirydate_can_null
 */
class m190411_045322_altering_column_expirydate_can_null extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('customer_recurring_payment', 'expiryDate', $this->date()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190411_045322_altering_column_expirydate_can_null cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190411_045322_altering_column_expirydate_can_null cannot be reverted.\n";

        return false;
    }
    */
}
