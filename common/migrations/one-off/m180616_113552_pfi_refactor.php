<?php
use yii\db\Migration;
use common\models\Invoice;
use common\models\Payment;
use common\models\User;
use common\models\Transaction;
use common\models\OpeningBalance;
use common\models\CustomerPayment;
use common\models\PaymentMethod;
/**
 * Class m180616_113552_pfi_refactor
 */
class m180616_113552_pfi_refactor extends Migration
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
        $transactionIds = [];
        $invoices = Invoice::find()
            ->notDeleted()
            ->andWhere(['NOT', ['invoice.user_id' => 0]])
            ->location([14, 15])
            ->all();
        foreach ($invoices as $invoice) {
            $transactionIds[] = $invoice->transactionId;
        }
        $payments = Payment::find()
            ->notDeleted()
            ->location([14, 15])
            ->all();
        foreach ($payments as $payment) {
            $transactionIds[] = $payment->transactionId;
        }
        $transactions = Transaction::find()
            ->andWhere(['id' => $transactionIds])
            ->orderBy(['id' => SORT_ASC])
            ->all();
        foreach ($transactions as $transaction) {
            if ($transaction->invoice) {
                $invoice = $transaction->invoice;
                if ($invoice->isInvoice()) {
                    $transaction = new Transaction();
                    $transaction->save();
                    $invoice->updateAttributes(['transactionId' => $transaction->id]);
                }
            } else if ($transaction->payment) {
                $payment = $transaction->payment;
                if (!$payment->isAutoPayments()) {
                    if ($payment->invoice) {
                        $transaction = new Transaction();
                        $transaction->save();
                        $payment->updateAttributes(['transactionId' => $transaction->id]);
                        $customerPayment = new CustomerPayment();
                        $customerPayment->userId = $payment->user_id;
                        $customerPayment->paymentId = $payment->id;
                        $customerPayment->save();
                        if ($payment->invoice->isInvoice()) {
                            $paymentModel = new Payment();
                            $paymentModel->amount = $payment->amount;
                            $paymentModel->date = $payment->date;
                            $payment->invoice->addPayment($payment->invoice->user, $paymentModel);
                        }
                        $payment->invoicePayment->delete();
                    }
                } else {
                    if ($payment->invoice) {
                        if ($payment->isCreditUsed()) {
                            if (!$payment->invoice->isInvoice()) {
                                $customerPayment = new CustomerPayment();
                                $customerPayment->userId = $payment->user_id;
                                $customerPayment->paymentId = $payment->id;
                                $customerPayment->save();
                                $payment->invoicePayment->delete();
                            } else if ($payment->debitPayment->invoice) {
                                if (!$payment->debitPayment->invoice->isInvoice()) {
                                    $customerPayment = new CustomerPayment();
                                    $customerPayment->userId = $payment->user_id;
                                    $customerPayment->paymentId = $payment->debitPayment->id;
                                    $customerPayment->save();
                                    $payment->debitPayment->invoicePayment->delete();
                                }
                            }
                        }
                        $transaction = new Transaction();
                        $transaction->save();
                        $payment->updateAttributes(['transactionId' => $transaction->id]);
                    } else if ($payment->lesson) {
                        $transaction = new Transaction();
                        $transaction->save();
                        $payment->updateAttributes(['transactionId' => $transaction->id]);
                    }
                }
            }
        }
        
        $realInvoices = Invoice::find()
            ->notDeleted()
            ->notCanceled()
            ->andWhere(['NOT', ['invoice.user_id' => 0]])
            ->location([14, 15])
            ->notReturned()
            ->invoice()
            ->all();
        foreach ($realInvoices as $realInvoice) {
            $realInvoice->save();
        }

        $transactions = Transaction::find()
            ->joinWith('payment')
            ->joinWith('invoice')
            ->andWhere(['AND', ['invoice.transactionId' => null], ['payment.transactionId' => null]])
            ->all();

        foreach ($transactions as $transaction) {
            $transaction->delete();
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
