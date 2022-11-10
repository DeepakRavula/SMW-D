<?php

use yii\db\Migration;
use common\models\Payment;
use common\models\InvoicePayment;
use common\models\User;
/**
 * Class m180927_145453_delete_invoice_payment
 */
class m180927_145453_delete_invoice_payment extends Migration
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
        $payments = Payment::find()
            ->andWhere(['id' => [7847,7587]])
            ->all();
        foreach ($payments as $payment) {
            $payment->delete();
        }
        $invoicePayments = InvoicePayment::find()
                ->andWhere(['invoice_id' => [1998,1999]])
                ->all();
        foreach ($invoicePayments as $invoicePayment) {
            $invoicePayment->payment->delete();
        }      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180927_145453_delete_invoice_payment cannot be reverted.\n";

        return false;
    }
}
