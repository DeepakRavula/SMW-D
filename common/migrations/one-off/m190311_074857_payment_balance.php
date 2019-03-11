<?php

use yii\db\Migration;
use common\models\Payment;

/**
 * Class m190311_074857_payment_balance
 */
class m190311_074857_payment_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $payments = Payment::find()
            ->notDeleted()
            ->all();
        foreach ($payments as $payment) {
            $payment->updateAttributes(['balance' => $payment->getBalanceAmount()]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190311_074857_payment_balance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190311_074857_payment_balance cannot be reverted.\n";

        return false;
    }
    */
}
