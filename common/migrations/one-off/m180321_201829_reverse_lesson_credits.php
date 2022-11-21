<?php

use yii\db\Migration;
use common\models\User;
use common\models\Invoice;
use common\models\Payment;

/**
 * Class m180317_201829_reverse_lesson_credits
 */
class m180321_201829_reverse_lesson_credits extends Migration
{
    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $payments = Payment::find()
            ->notDeleted()
            ->creditUsed()
            ->joinWith(['invoice' => function ($query) {
                $query->proFormaInvoice()->deleted();
            }])
            ->all();        
        foreach ($payments as $payment) {
            $payment->delete();
        }
        $payments = Payment::find()
            ->notDeleted()
            ->creditUsed()
            ->joinWith(['invoice' => function ($query) {
                $query->proFormaInvoice()->notDeleted()->unpaid();
            }])
            ->all();
        foreach ($payments as $payment) {
            $payment->delete();
            $payment->invoice->updateAttributes(['isPosted' => false]);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180321_201829_reverse_lesson_credits cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180317_201829_reverse_lesson_credits cannot be reverted.\n";

        return false;
    }
    */
}
