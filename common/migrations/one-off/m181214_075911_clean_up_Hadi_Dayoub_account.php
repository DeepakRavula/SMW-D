<?php

use yii\db\Migration;
use common\models\InvoicePayment;
use common\models\Invoice;
use common\models\User;

/**
 * Class m181214_075911_clean_up_Hadi_Dayoub_account
 */
class m181214_075911_clean_up_Hadi_Dayoub_account extends Migration
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
        $invoicePaymentIds = [8583,10355];
        $invoicePayments = InvoicePayment::find()
                ->andWhere(['id' => $invoicePaymentIds])
                ->all();
        foreach ($invoicePayments as $invoicePayment) {
            $invoicePayment->delete();
        }
        $invoiceIds = [5096, 6709];
        $invoices = Invoice::find()
                ->andWhere(['id' => $invoiceIds])
                ->all();
        foreach ($invoices as $invoice) {
            $invoice->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181214_075911_clean_up_Hadi_Dayoub_account cannot be reverted.\n";

        return false;
    }
}
