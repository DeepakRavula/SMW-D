<?php

use yii\db\Migration;
use common\models\User;
use common\models\Lesson;
use common\models\LessonPayment;

/**
 * Class m180801_085505_lesson_exlpode_discount_fix
 */
class m180801_085505_lesson_exlpode_discount_fix extends Migration
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
            ->isConfirmed()
            ->deleted()
            ->invoiced()
            ->location([14, 15])
            ->all();
        
        foreach ($lessons as $lesson) {
            if ($lesson->hasInvoice()) {
                $lessonPayments = LessonPayment::find()
                    ->joinWith(['payment' => function ($query) {
                        $query->deleted()
                            ->notCreditUsed();
                    }])
                    ->andWhere(['lesson_payment.lessonId' => $lesson->id, 'lesson_payment.enrolmentId' => $lesson->enrolment->id])
                    ->deleted()
                    ->all();
                foreach ($lessonPayments as $lessonPayment) {
                    $lessonPayment->payment->updateAttributes(['isDeleted' => false]);
                    $lessonPayment->updateAttributes(['isDeleted' => false]);
                    if ($lessonPayment->payment->creditUsage) {
                        $lessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['isDeleted' => false]);
                        if ($lessonPayment->payment->creditUsage->debitUsagePayment->allInvoicePayment) {
                            $lessonPayment->payment->creditUsage->debitUsagePayment->allInvoicePayment->updateAttributes(['isDeleted' => false]);
                            $lessonPayment->payment->creditUsage->debitUsagePayment->allInvoicePayment->invoice->save();
                        }
                    }
                }
                $lesson->updateAttributes([
                    'isDeleted' => false,
                    'status' => Lesson::STATUS_SCHEDULED
                ]);
                $lesson->save();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180801_085505_lesson_exlpode_discount_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180801_085505_lesson_exlpode_discount_fix cannot be reverted.\n";

        return false;
    }
    */
}
