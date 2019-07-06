<?php

use yii\db\Migration;
use common\models\Invoice;
use common\models\User;

/**
 * Class m190706_055111_data_fix_for_voided_invoice
 */
class m190706_055111_data_fix_for_voided_invoice extends Migration
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
        $invoices = Invoice::find()
                ->notDeleted()
                ->andWhere(['isVoid' => 1])
                ->all();
        foreach ($invoices as $invoice) {
            $invoice->tax = 0.00;
            $invoice->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190706_055111_data_fix_for_voided_invoice cannot be reverted.\n";

        return false;
    }
}
