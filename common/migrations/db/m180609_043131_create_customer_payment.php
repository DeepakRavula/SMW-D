<?php

use yii\db\Migration;

/**
 * Class m180609_043131_create_customer_payment
 */
class m180609_043131_create_customer_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('customer_payment', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'paymentId' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180609_043131_create_customer_payment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180609_043131_create_customer_payment cannot be reverted.\n";

        return false;
    }
    */
}
