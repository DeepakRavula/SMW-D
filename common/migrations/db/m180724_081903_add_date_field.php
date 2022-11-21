<?php

use yii\db\Migration;
use common\models\LessonPayment;
use common\models\InvoicePayment;
use common\models\Payment;

/**
 * Class m180724_081903_add_date_field
 */
class m180724_081903_add_date_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('lesson_payment', 'date', $this->timeStamp()->notNull());
        $this->addColumn('invoice_payment', 'date', $this->timeStamp()->notNull());
        $lessonPayments =  LessonPayment::find()
                    ->notDeleted()
                    ->all();
        foreach ($lessonPayments as $lessonPayment) {
            $payment = Payment::findOne($lessonPayment->paymentId);
            if ($payment) {
                $lessonPayment->updateAttributes(['date' => $payment->createdOn ?? $payment->date]);
            }
        }
        $invoicePayments = InvoicePayment::find()
                    ->notDeleted()
                    ->all();
        foreach ($invoicePayments as $invoicePayment) {
            $payment = Payment::findOne($invoicePayment->payment_id);
            if ($payment) {
                $invoicePayment->updateAttributes(['date' => $payment->createdOn ?? $payment->date]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180724_081903_add_date_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180724_081903_add_date_field cannot be reverted.\n";

        return false;
    }
    */
}
