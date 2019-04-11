<?php

use yii\db\Migration;

/**
 * Class m190410_073535_add_isRecurringPaymentEnabled_column
 */
class m190410_073535_add_isRecurringPaymentEnabled_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer_recurring_payment', 'isRecurringPaymentEnabled',  $this->boolean()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190410_073535_add_isRecurringPaymentEnabled_column cannot be reverted.\n";

        return false;
    }
}
