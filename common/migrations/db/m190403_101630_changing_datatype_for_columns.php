<?php

use yii\db\Migration;

/**
 * Class m190403_101630_changing_datatype_for_columns
 */
class m190403_101630_changing_datatype_for_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('customer_recurring_payment', 'entryDay');
        $this->dropColumn('customer_recurring_payment', 'paymentDay');
        $this->addColumn('customer_recurring_payment', 'entryDay', $this->integer()->notNull()->after('customerId'));
        $this->addColumn('customer_recurring_payment', 'paymentDay',  $this->integer()->notNull()->after('entryDay'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190403_101630_changing_datatype_for_columns cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190403_101630_changing_datatype_for_columns cannot be reverted.\n";

        return false;
    }
    */
}
