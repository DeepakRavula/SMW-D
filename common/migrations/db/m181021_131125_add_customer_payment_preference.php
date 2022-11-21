<?php

use yii\db\Migration;

/**
 * Class m181021_131125_add_customer_payment_preference
 */
class m181021_131125_add_customer_payment_preference extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer_payment_preference', 'isPreferredPaymentEnabled', $this->boolean()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181021_131125_add_customer_payment_preference cannot be reverted.\n";

        return false;
    }
}
