<?php

use yii\db\Migration;

/**
 * Class m190401_090335_add_fields_customer_recurring_payment_enrolment
 */
class m190401_090335_add_fields_customer_recurring_payment_enrolment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer_recurring_payment_enrolment', 'customerId', $this->integer()->notNull()->after('enrolmentId'));
        $this->addColumn('customer_recurring_payment_enrolment', 'entryDay', $this->integer()->notNull()->after('customerId'));
        $this->addColumn('customer_recurring_payment_enrolment', 'paymentDay',  $this->integer()->notNull()->after('entryDay'));
        $this->addColumn('customer_recurring_payment_enrolment', 'paymentMethodId', $this->integer()->notNull()->after('paymentDay'));
        $this->addColumn('customer_recurring_payment_enrolment', 'paymentFrequencyId', $this->integer()->notNull()->after('paymentMethodId'));
        $this->addColumn('customer_recurring_payment_enrolment', 'expiryDate', $this->date()->notNull()->after('paymentFrequencyId'));
        $this->addColumn('customer_recurring_payment_enrolment', 'amount', $this->decimal(10, 4)->notNull()->defaultValue(0.00)->after('expiryDate'));
        $this->dropColumn('customer_recurring_payment_enrolment', 'customerRecurringPaymentId');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_090335_add_fields_customer_recurring_payment_enrolment cannot be reverted.\n";

        return false;
    }
}
