<?php

use yii\db\Migration;
use common\models\User;
use common\models\Lesson;
use common\models\Payment;

/**
 * Class m180607_050215_deleted_lesson_credit_fix
 */
class m180607_050215_deleted_lesson_credit_fix extends Migration
{
    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $lessons = Lesson::find()
            ->deleted()
            ->location([14, 15])
            ->privateLessons()
            ->all();
        foreach ($lessons as $lesson) {
            if (!$lesson->hasCreditApplied($lesson->enrolment->id)) {
                if ($lesson->proFormaLineItem) {
                    $lesson->proFormaLineItem->delete();
                }
            } else if (!$lesson->hasCreditUsed($lesson->enrolment->id)) {
                $invoice = $lesson->addLessonCreditInvoice();
                $payment = new Payment();
                $payment->amount = $lesson->getLessonCreditAmount($lesson->enrolment->id);
                $invoice->addPayment($lesson, $payment, $lesson->enrolment);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180607_050215_deleted_lesson_credit_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180607_050215_deleted_lesson_credit_fix cannot be reverted.\n";

        return false;
    }
    */
}
