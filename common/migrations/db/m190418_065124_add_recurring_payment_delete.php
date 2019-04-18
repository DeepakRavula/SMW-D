<?php

use yii\db\Migration;

/**
 * Class m190418_065124_add_recurring_payment_delete
 */
class m190418_065124_add_recurring_payment_delete extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'customer_recurring_payment',
            'isDeleted',
            $this->integer()->notNull()->after('amount')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190418_065124_add_recurring_payment_delete cannot be reverted.\n";

        return false;
    }
}
