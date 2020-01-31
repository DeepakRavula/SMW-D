<?php

use common\models\InvoicePayment;
use common\models\Lesson;
use common\models\LessonPayment;
use common\models\Payment;
use yii\db\Migration;
use common\models\User;

/**
 * Class m200128_070706_fix_lesson_payment_details
 */
class m200128_070706_fix_lesson_payment_details extends Migration
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
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $cancelledLessonIds = [707248,707252];
        $payment = Payment::findOne(66142);
        $lessonPayments = LessonPayment::find()
                            ->andWhere(['paymentId' => $payment->id])
                            ->andWhere(['NOT IN', 'lessonId', $cancelledLessonIds])
                            ->all();
        $invoicePayments = InvoicePayment::find()
                            ->andWhere(['payment_id' => $payment->id])
                            ->all();
        print_r('Lesson Payments');
        foreach ($lessonPayments as $lessonPayment) {
           $lessonPayment->isDeleted =  false;
           $lessonPayment->save();
        }
        print_r('Invoice Payments');
        foreach ($invoicePayments as $invoicePayment) {
            $invoicePayment->isDeleted =  false;
            $invoicePayment->save();
        }

        $lessonPayment = LessonPayment::findOne(56998);
        $lessonPayment->lessonId = 1097171;
        $lessonPayment->save();
        $courseIds = [3291,8723,3292,5808];
        $lessons = Lesson::find()
        ->notDeleted()
        ->privateLessons()
        ->isConfirmed()
        ->andWhere(['IN', 'courseId', $courseIds])
        ->all();
        $payment->isDeleted = false;
        $payment->save();
        $totalAmountPaid = 0;
        //print_r($lessons);die('coming');
        // print_r("\nlesson id | Student     | Lesson Date  | invoice     | Paid with  |");
        // print_r("\n----------|-------------|---------------|------------|------------|");
        // die('coming');
        foreach ($lessons as $lesson) {
            $lesson->save();
        }
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200128_070706_fix_lesson_payment_details cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200128_070706_fix_lesson_payment_details cannot be reverted.\n";

        return false;
    }
    */
}
