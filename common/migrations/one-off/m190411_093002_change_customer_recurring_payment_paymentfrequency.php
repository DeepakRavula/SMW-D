<?php

use yii\db\Migration;
use common\models\CustomerRecurringPayment;

/**
 * Class m190411_093002_change_customer_recurring_payment_paymentfrequency
 */
class m190411_093002_change_customer_recurring_payment_paymentfrequency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $recurringPayments = CustomerRecurringPayment::find()
                    ->all();
        foreach ($recurringPayments as $recurringPayment) {
            if ($recurringPayment->paymentFrequencyId == 0) {
                $recurringPayment->updateAttributes(['paymentFrequencyId' => 1]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190411_093002_change_customer_recurring_payment_paymentfrequency cannot be reverted.\n";

        return false;
    }
}
