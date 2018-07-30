<?php

use yii\db\Migration;
use common\models\User;
use common\models\Lesson;
use common\models\Invoice;

/**
 * Class m180729_094556_fix_lesson_payment_duplication
 */
class m180729_094556_fix_lesson_payment_duplication extends Migration
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
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $lessons = Lesson::find()
            ->location([14, 15])
            ->joinWith(['lessonPayment' => function ($query) {
                $query->andWhere(['NOT', ['lesson_payment.id' => null]]);
            }])
            ->all();
        foreach ($lessons as $lesson) {
            foreach ($lesson->lessonPayments as $rootLessonPayment) {
                foreach ($lesson->lessonPayments as $lessonPayment) {
                    if (!$rootLessonPayment->isDeleted && $rootLessonPayment->id != $lessonPayment->id) {
                        if ($rootLessonPayment->paymentId == $lessonPayment->paymentId) {
                            $rootLessonPayment->updateAttributes(['amount' => $lessonPayment->amount + $rootLessonPayment->amount]);
                            $lessonPayment->updateAttributes(['isDeleted' => true]);
                        }
                    }
                }
            }
        }

        $invoices = Invoice::find()
            ->location([14, 15])
            ->joinWith(['invoicePayment' => function ($query) {
                $query->andWhere(['NOT', ['invoice_payment.id' => null]]);
            }])
            ->all();
        foreach ($invoices as $invoice) {
            foreach ($invoice->invoicePayments as $rootInvoicePayment) {
                foreach ($invoice->invoicePayments as $invoicePayment) {
                    if (!$rootInvoicePayment->isDeleted && $rootInvoicePayment->id != $invoicePayment->id) {
                        if ($rootInvoicePayment->payment_id == $invoicePayment->payment_id) {
                            $rootInvoicePayment->updateAttributes(['amount' => $invoicePayment->amount + $rootInvoicePayment->amount]);
                            $invoicePayment->updateAttributes(['isDeleted' => true]);
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180729_094556_fix_lesson_payment_duplication cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180729_094556_fix_lesson_payment_duplication cannot be reverted.\n";

        return false;
    }
    */
}
