<?php
use yii\db\Migration;
use common\models\Invoice;
use common\models\User;
/**
 * Class m180616_113552_pfi_refactor
 */
class m180617_113555_old_system_migration_pfi extends Migration
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
        
        $invoices = Invoice::find()
            ->notDeleted()
            ->location([14, 15])
            ->andWhere(['<', 'balance', 0])
            ->andWhere(['NOT', ['invoice.user_id'=> 0]])
            ->manualPayments()
            ->all();
        foreach ($invoices as $invoice) {
            foreach ($invoice->manualPayments as $payment) {
                $balance = abs($invoice->balance);
                if ($payment->amount <= $balance) {
                    $balance -= $payment->amount;
                    $payment->delete();
                    $invoice->save();
                } else {
                    $payment->updateAttributes(['amount' => $payment->amount - $balance]);
                    $invoice->save();
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
