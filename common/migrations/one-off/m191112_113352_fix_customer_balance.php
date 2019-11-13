<?php

use yii\db\Migration;
use common\models\User;
use common\models\Payment;
/**
 * Class m191112_113352_fix_customer_balance
 */
class m191112_113352_fix_customer_balance extends Migration
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
        $payment = Payment::findOne(71719);
        $payment->save(false);
        $payment->customer->updateCustomerBalance();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191112_113352_fix_customer_balance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191112_113352_fix_customer_balance cannot be reverted.\n";

        return false;
    }
    */
}
