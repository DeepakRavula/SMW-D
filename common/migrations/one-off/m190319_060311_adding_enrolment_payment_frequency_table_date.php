<?php

use yii\db\Migration;
use common\models\Enrolment;
use common\models\EnrolmentPaymentFrequency;
use common\models\User;

/**
 * Class m190319_060311_adding_enrolment_payment_frequency_table_date
 */
class m190319_060311_adding_enrolment_payment_frequency_table_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function safeUp()
    {
        $enrolments = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->isRegular()
                    ->privateProgram()
                    ->all();
        foreach ($enrolments as $enrolment) {
            $enrolmentPaymentFrequency = new EnrolmentPaymentFrequency();
            $enrolmentPaymentFrequency->enrolmentId = $enrolment->id;
            $enrolmentPaymentFrequency->paymentFrequencyId = $enrolment->paymentFrequencyId;
            $enrolmentPaymentFrequency->paymentCycleStartDate = $enrolment->firstPaymentCycle->startDate;
            $enrolmentPaymentFrequency->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190319_060311_adding_enrolment_payment_frequency_table_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190319_060311_adding_enrolment_payment_frequency_table_date cannot be reverted.\n";

        return false;
    }
    */
}
