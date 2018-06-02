<?php

use yii\db\Migration;
use common\models\Invoice;
use common\models\User;

/**
 * Class m180523_065659_opening_balance_refactor
 */
class m180523_065659_opening_balance_refactor extends Migration
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
        $invoices = Invoice::find()
            ->notDeleted()
            ->openingBalance()
            ->andWhere(['invoice.total' => 0.0000])
            ->all();
        foreach ($invoices as $invoice) {
            if ($invoice->hasAccountEntryPayment()) {
                $invoice->lineItem->updateAttributes([
                    'unit' => -1,
                    'amount' => $invoice->accountEntryPayment->amount
                ]);
                $invoice->accountEntryPayment->delete();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180523_065659_opening_balance_refactor cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180523_065659_opening_balance_refactor cannot be reverted.\n";

        return false;
    }
    */
}
