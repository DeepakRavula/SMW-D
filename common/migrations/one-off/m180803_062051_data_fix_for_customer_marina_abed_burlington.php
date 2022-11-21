<?php

use yii\db\Migration;
use common\models\InvoicePayment;
use common\models\Payment;
use common\models\User;
/**
 * Class m180803_062051_data_fix_for_customer_marina_abed_burlington
 */
class m180803_062051_data_fix_for_customer_marina_abed_burlington extends Migration
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
        $invoicePaymentIds = ['28741', '18543'];
        $invoicePayments = InvoicePayment::find()
            ->andWhere(['payment_id' => $invoicePaymentIds])->all();
        
        foreach ($invoicePayments as $invoicePayment) {
            if ($invoicePayment->invoice_id != 6140) {
                $invoicePayment->updateAttributes(['isDeleted' => true]);
            } else {
                $invoicePayment->updateAttributes(['amount' =>  $invoicePayment->payment->amount]);
            }
        }
        $payment = Payment::findOne(28741);
        $payment->updateAttributes(['isDeleted' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180803_062051_data_fix_for_customer_marina_abed_burlington cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180803_062051_data_fix_for_customer_marina_abed_burlington cannot be reverted.\n";

        return false;
    }
    */
}
