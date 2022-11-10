<?php

use yii\db\Migration;
use common\models\PaymentCycle;
/**
 * Class m180901_063914_fix_payment_cycle_duplication
 */
class m180901_063914_fix_payment_cycle_duplication extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $duplicatePaymentCycleIds = ['2242','2243','2244','2245','11932'];
        $paymentCycles = PaymentCycle::find()
            ->andWhere(['id' => $duplicatePaymentCycleIds])
            ->all();
        foreach($paymentCycles as $paymentCycle) {
            $paymentCycle->delete();
        }  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180901_063914_fix_payment_cycle_duplication cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180901_063914_fix_payment_cycle_duplication cannot be reverted.\n";

        return false;
    }
    */
}
