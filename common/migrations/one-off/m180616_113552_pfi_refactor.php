<?php
use yii\db\Migration;
use common\models\Invoice;
use common\models\Payment;
use common\models\User;
use common\models\Transaction;
use common\models\OpeningBalance;
use common\models\CustomerPayment;
use common\models\PaymentMethod;
use common\models\InvoicePayment;
use common\models\LessonPayment;
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
	    ini_set('memory_limit', '-1');
        $invoicePayments = InvoicePayment::find()->all();
        $lessonPayments = LessonPayment::find()->all();
        foreach ($invoicePayments as $invoicePayment) {
            if ($invoicePayment->payment) {
                $invoicePayment->updateAttributes(['amount' => $invoicePayment->payment->amount]);
            }
        }
        foreach ($lessonPayments as $lessonPayment) {
            if ($lessonPayment->payment) {
                $lessonPayment->updateAttributes(['amount' => $lessonPayment->payment->amount]);
            }
        }
        $invoices = Invoice::find()
            ->notDeleted()
            ->location([14, 15])
            ->andWhere(['<', 'balance', 0])
            ->andWhere(['NOT', ['invoice.user_id'=> 0]])
            ->hasManualPayments()
            ->all();
        foreach ($invoices as $invoice) {
            $amount = 0;
            if ($invoice->hasManualPayments()) {
                foreach ($invoice->manualPayments as $payment) {
                    $amount += $payment->amount;
                }
            }
            if ($amount > $invoice->total) {
                foreach ($invoice->manualPayments as $payment) {
                    $balance = abs($invoice->balance);
                    if ($payment->amount < $invoice->balance) {
                        $balance = $payment->amount - abs($invoice->balance);
                        $payment->delete();
                    } else {
                        $payment->invoicePayment->updateAttributes(['amount' => $balance]);
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
