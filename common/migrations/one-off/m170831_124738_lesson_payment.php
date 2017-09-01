<?php

use yii\db\Migration;
use common\models\Invoice;
use common\models\Payment;
use common\models\LessonPayment;
use common\models\PaymentMethod;

class m170831_124738_lesson_payment extends Migration
{
    public function up()
    {
        $pfis = Invoice::find()
                ->notCanceled()
                ->notDeleted()
                ->proFormaInvoice()
                ->all();
        foreach ($pfis as $pfi) {
            $payments = Payment::find()
                    ->notDeleted()
                    ->joinWith('invoicePayment')
                    ->where(['invoice_payment.invoice_id' => $pfi->id])
                    ->creditUsed()
                    ->all();
            foreach ($payments as $payment) {
                $invoice = Invoice::findOne($payment->reference);
                $lessonPayment = new LessonPayment();
                $lessonPayment->lessonId = $invoice->lineItem->lesson->id;
                $lessonPayment->paymentId = $payment->debitUsage->credit_payment_id;
                $lessonPayment->enrolmentId = $invoice->lineItem->lesson->enrolment->id;
                if ($invoice->lineItem->lineItemEnrolment) {
                    $lessonPayment->enrolmentId = $invoice->lineItem->lineItemEnrolment->enrolmentId;
                }
                $lessonPayment->save();
                $payment->updateAttributes(['reference' => $invoice->lineItem->lesson->id]);
                $paymentModel = new Payment();
                $paymentModel->amount = $payment->amount;
                $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
                $paymentModel->reference = $payment->invoice->id;
                $paymentModel->lessonId = $invoice->lineItem->lesson->id;
                $paymentModel->save();
            }
        }
        
        $invoices = Invoice::find()
                ->notCanceled()
                ->notDeleted()
                ->invoice()
                ->all();
        foreach ($invoices as $invoice) {
            $payments = Payment::find()
                    ->notDeleted()
                    ->joinWith('invoicePayment')
                    ->where(['invoice_payment.invoice_id' => $invoice->id])
                    ->creditApplied()
                    ->all();
            foreach ($payments as $payment) {
                $invoice = $payment->invoice;
                $lessonPayment = new LessonPayment();
                $lessonPayment->lessonId = $invoice->lineItem->lesson->id;
                $lessonPayment->paymentId = $payment->creditUsage->debit_payment_id;
                $lessonPayment->enrolmentId = $invoice->lineItem->lesson->enrolment->id;
                if ($invoice->lineItem->lineItemEnrolment) {
                    $lessonPayment->enrolmentId = $invoice->lineItem->lineItemEnrolment->enrolmentId;
                }
                $lessonPayment->save();
                $payment->updateAttributes(['reference' => $invoice->lineItem->lesson->id]);
                $paymentModel = new Payment();
                $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
                $paymentModel->lessonId = $invoice->lineItem->lesson->id;
                $paymentModel->reference = $invoice->id;
                $paymentModel->save();
            }
        }
    }

    public function down()
    {
        echo "m170831_124738_lesson_payment cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
