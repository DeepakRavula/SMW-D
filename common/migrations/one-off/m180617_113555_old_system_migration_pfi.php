<?php
use yii\db\Migration;
use common\models\Invoice;
use common\models\User;
use common\models\InvoicePayment;
use common\models\LessonPayment;
use common\models\InvoiceLineItem;
use common\models\discount\LessonDiscount;
use common\models\Lesson;
/**
 * Class m180616_113552_pfi_refactor
 */
class m180617_113555_old_system_migration_pfi extends Migration
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
        
        $proformaInvoices = Invoice::find()
            ->notDeleted()
            ->proFormaInvoice()
            ->location([14, 15])
            ->andWhere(['NOT', ['invoice.user_id'=> 0]])
            ->appliedPayments()
            ->all();
        foreach ($proformaInvoices as $proformaInvoice) {
            if (!$proformaInvoice->hasCreditUsed()) {
                foreach ($proformaInvoice->invoicePayments as $payment) {
                    $payment->delete();
                }
            } else if (!$proformaInvoice->hasLessonCreditUsedPayment()) {
                $appliedAmount = $proformaInvoice->invoiceAppliedPaymentTotal;
                if ($appliedAmount > $proformaInvoice->creditUsedPaymentTotal) {
                    $amountToReduce = $appliedAmount - abs($proformaInvoice->creditUsedPaymentTotal);
                    foreach ($proformaInvoice->manualPayments as $payment) {
                        if ($payment->amount <= $amountToReduce) {
                            $amountToReduce -= $payment->amount;
                            $payment->delete();
                            $proformaInvoice->save();
                        } else {
                            $payment->updateAttributes(['amount' => $payment->amount - $amountToReduce]);
                            $amountToReduce -= $payment->amount;
                            $payment->invoice->save();
                        }
                    }
                    foreach ($proformaInvoice->creditAppliedPayments as $payment) {
                        if ($payment->amount <= $amountToReduce) {
                            $amountToReduce -= $payment->amount;
                            $payment->delete();
                            $proformaInvoice->save();
                        } else {
                            $payment->updateAttributes(['amount' => $payment->amount - $amountToReduce]);
                            if ($payment->payment->creditUsage->debitUsagePayment) {
                                $payment->payment->creditUsage->debitUsagePayment->updateAttributes(['amount' => - ($payment->amount - $amountToReduce)]);
                                if ($payment->payment->creditUsage->debitUsagePayment->invoicePayment) {
                                    $payment->payment->creditUsage->debitUsagePayment->invoicePayment->updateAttributes(['amount' => - ($payment->amount - $amountToReduce)]);
                                } else if ($payment->payment->creditUsage->debitUsagePayment->lessonPayment) {
                                    $payment->payment->creditUsage->debitUsagePayment->lessonPayment->updateAttributes(['amount' => - ($payment->amount - $amountToReduce)]);
                                }
                            }
                            $amountToReduce -= $payment->amount;
                            $payment->invoice->save();
                        }
                    }
                }
            }
        }

        $invoices = Invoice::find()
            ->notDeleted()
            ->location([14, 15])
            ->andWhere(['<', 'balance', 0])
            ->andWhere(['NOT', ['invoice.user_id'=> 0]])
            ->manualPayments()
            ->all();
        foreach ($invoices as $invoice) {
            $amount = $invoice->invoiceAppliedPaymentTotal;
            if ($amount > $invoice->total) {
                foreach ($invoice->manualPayments as $payment) {
                    $balance = abs($invoice->balance);
                    if ($payment->amount <= $balance) {
                        $balance -= $payment->amount;
                        $payment->delete();
                        $invoice->save();
                    } else {
                        $payment->updateAttributes(['amount' => $payment->amount - $balance]);
                        $invoice->save();
                    }
                }
            }
            $invoice->save();
        }
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180616_113552_pfi_refactor cannot be reverted.\n";
        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }
    public function down()
    {
        echo "m180616_113552_pfi_refactor cannot be reverted.\n";
        return false;
    }
    */
}
