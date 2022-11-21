<?php

use yii\db\Migration;

/**
 * Class m180918_181013_payment_cycle_disable
 */
class m180918_181013_payment_cycle_disable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_cycle', 'isPreferredPaymentEnabled', $this->boolean()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180918_181013_payment_cycle_disable cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180918_181013_payment_cycle_disable cannot be reverted.\n";

        return false;
    }
    */
}
