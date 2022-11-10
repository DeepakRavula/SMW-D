<?php

use yii\db\Migration;

/**
 * Class m180409_065759_add_expiry_customer_payment_preference
 */
class m180409_065759_add_expiry_customer_payment_preference extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer_payment_preference', 'expiryDate', $this->date()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180409_065759_add_expiry_customer_payment_preference cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180409_065759_add_expiry_customer_payment_preference cannot be reverted.\n";

        return false;
    }
    */
}
