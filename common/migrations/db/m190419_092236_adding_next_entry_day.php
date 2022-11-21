<?php

use yii\db\Migration;
use Carbon\Carbon;
use common\models\CustomerRecurringPayment;

/**
 * Class m190419_092236_adding_next_entry_day
 */
class m190419_092236_adding_next_entry_day extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer_recurring_payment', 'nextEntryDay',  $this->date());
        

        $customerRecurringPayments = CustomerRecurringPayment::Find()
                                        ->all();
        $currentDate = Carbon::now()->format('Y-m-d');
        foreach ($customerRecurringPayments as $customerRecurringPayment) {
            if (Carbon::parse($customerRecurringPayment->startDate)->format('Y-m-d') <= $currentDate ) {
                $nextEntryDay = Carbon::parse($customerRecurringPayment->startDate)->modify('+'.$customerRecurringPayment->paymentFrequencyId.'months')->format('Y-m-d');
            } else {
                $nextEntryDay = Carbon::parse($customerRecurringPayment->startDate)->format('Y-m-d');
            }
            $customerRecurringPayment->updateAttributes(['nextEntryDay' => $nextEntryDay]);

        }              

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190419_092236_adding_next_entry_day cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190419_092236_adding_next_entry_day cannot be reverted.\n";

        return false;
    }
    */
}
