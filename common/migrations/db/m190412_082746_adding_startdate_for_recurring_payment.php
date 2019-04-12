<?php

use yii\db\Migration;
use common\models\CustomerRecurringPayment;
use Carbon\Carbon;

/**
 * Class m190412_082746_adding_startdate_for_recurring_payment
 */
class m190412_082746_adding_startdate_for_recurring_payment extends Migration
{
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer_recurring_payment', 'startDate',  $this->date());

        $customerRecurringPayments = CustomerRecurringPayment::Find()
                                        ->all();
        foreach ($customerRecurringPayments as $customerRecurringPayment) {
            $customerRecurringPayment->updateAttributes(['startDate' => Carbon::now()->format('Y-m-d')]);

        }                                
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190412_082746_adding_startdate_for_recurring_payment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190412_082746_adding_startdate_for_recurring_payment cannot be reverted.\n";

        return false;
    }
    */
}
