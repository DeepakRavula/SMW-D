<?php

use yii\db\Migration;
use common\models\Invoice;
use common\models\User;
use common\models\Payment;

/**
 * Class m191118_101826_data_fix_void_invoice
 */
class m191118_101826_data_fix_void_invoice extends Migration
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
        $invoiceIds = [162108, 162109];
        $invoices = Invoice::find()
                    ->andWhere(['IN', 'invoice.id',$invoiceIds])
                    ->all();
        foreach ($invoices as $invoice){
            $invoice->void(false);
        }

        $payment = Payment::findOne(297807);
        $lessonPayments = $payment->lessonPayments;
        $invoicePayments = $payment->invoicePayments;
        foreach($lessonPayments as $lessonPayment){
            $lessonPayment->delete();
        }
        foreach($invoicePayments as $invoicePayment){
            $invoicePayment->delete();
        }
        $payment->delete();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191118_101826_data_fix_void_invoice cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191118_101826_data_fix_void_invoice cannot be reverted.\n";

        return false;
    }
    */
}
