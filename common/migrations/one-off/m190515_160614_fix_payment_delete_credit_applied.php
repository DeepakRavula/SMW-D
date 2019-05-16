<?php

use yii\db\Migration;
use common\models\Payment;
use common\models\User;
use common\models\LessonPayment;

/**
 * Class m190515_160614_fix_payment_delete_credit_applied
 */
class m190515_160614_fix_payment_delete_credit_applied extends Migration
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
        $payment = Payment::findOne(102359);
        $lessonPayments = $payment->lessonPayments;
        foreach ($lessonPayments as $lessonPayment){
                $lessonPayment->delete();
        }
        $payment->delete();
        $applyingPayment = Payment::findOne(68369);
        $newLessonPayment = new LessonPayment();
        $newLessonPayment->amount = $applyingPayment->balance;
        $newLessonPayment->lessonId = 485189;
        $newLessonPayment->enrolmentId = 3573;
        $newLessonPayment->paymentId = $applyingPayment->id;
        $newLessonPayment->save();

      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190515_160614_fix_payment_delete_credit_applied cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190515_160614_fix_payment_delete_credit_applied cannot be reverted.\n";

        return false;
    }
    */
}
