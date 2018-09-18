<?php

use yii\db\Migration;
use common\models\Location;
use common\models\PaymentCycle;
use common\models\Enrolment;

/**
 * Class m180918_184351_enabling_payment_cycle_for_preferred_payment_customers
 */
class m180918_184351_enabling_payment_cycle_for_preferred_payment_customers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $enrolments = Enrolment::find()
            ->notDeleted()
            ->isConfirmed()
            ->privateProgram()
            ->andWhere(['NOT', ['enrolment.paymentFrequencyId' => 0]])
            ->isRegular()
            ->joinWith(['course' => function ($query){
                $query->location([14, 15, 16])
                        ->confirmed();
            }])
            ->paymentPrefered()
            ->all();
        foreach($enrolments as $enrolment) {
           $paymentCycles = PaymentCycle::find()
            ->notDeleted()
            ->andWhere(['enrolmentId' => $enrolment->id])
            ->all();
            
           foreach($paymentCycles as $paymentCycle) {
               $paymentCycle->updateAttributes(['isEnabled' => true]);
           } 
        }    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180918_184351_enabling_payment_cycle_for_preferred_payment_customers cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180918_184351_enabling_payment_cycle_for_preferred_payment_customers cannot be reverted.\n";

        return false;
    }
    */
}
