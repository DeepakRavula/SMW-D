<?php

use yii\db\Migration;
use common\models\Invoice;
use common\models\Payment;
use common\models\LessonPayment;
use common\models\PaymentMethod;
use common\models\CreditUsage;

class m170831_124738_lesson_payment extends Migration
{
    public function up()
    {
        $this->addColumn(
            'credit_usage',
            'credit_payment_id1',
            $this->integer()->after('debit_payment_id')
        );
        $creditUsages = CreditUsage::find()->all();
        foreach ($creditUsages as $creditUsage) {
            $creditUsage->updateAttributes(['credit_payment_id1' => $creditUsage->credit_payment_id]);
        }
        $pfis = Invoice::find()
                ->where(['invoice.location_id' => 7])
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
                $payment->updateAttributes(['reference' => $invoice->lineItem->lesson->id]);
                $paymentModel = new Payment();
                $paymentModel->amount = abs($payment->amount);
                $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
                $paymentModel->reference = $payment->invoice->id;
                $paymentModel->lessonId = $invoice->lineItem->lesson->id;
                $paymentModel->save();
                $lessonPayment = new LessonPayment();
                $lessonPayment->lessonId = $invoice->lineItem->lesson->id;
                $lessonPayment->paymentId = $paymentModel->id;
                $lessonPayment->enrolmentId = $invoice->lineItem->lesson->enrolment->id;
                if ($invoice->lineItem->lineItemEnrolment) {
                    $lessonPayment->enrolmentId = $invoice->lineItem->lineItemEnrolment->enrolmentId;
                }
                $lessonPayment->save();
                $payment->debitUsage->credit_payment_id = $paymentModel->id;
                $payment->debitUsage->save();
            }
        }
        
        $invoices = Invoice::find()
                 ->where(['invoice.location_id' => 7])
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
                $payment->updateAttributes(['reference' => $invoice->lineItem->lesson->id]);
                $paymentModel = new Payment();
                $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
                $paymentModel->lessonId = $invoice->lineItem->lesson->id;
                $paymentModel->reference = $invoice->id;
                $paymentModel->amount = $payment->amount;
                $paymentModel->save();
                $lessonPayment = new LessonPayment();
                $lessonPayment->lessonId = $invoice->lineItem->lesson->id;
                $lessonPayment->paymentId = $paymentModel->id;
                $lessonPayment->enrolmentId = $invoice->lineItem->lesson->enrolment->id;
                if ($invoice->lineItem->lineItemEnrolment) {
                    $lessonPayment->enrolmentId = $invoice->lineItem->lineItemEnrolment->enrolmentId;
                }
                $lessonPayment->save();
                $payment->creditUsage1->debit_payment_id = $paymentModel->id;
                $payment->creditUsage1->save();
            }
        }
        $this->dropColumn('credit_usage', 'credit_payment_id1');
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
