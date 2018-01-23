<?php

use yii\db\Migration;
use common\models\PaymentMethod;

class m170829_044323_change_apply_credit extends Migration
{
    public function up()
    {
        $applyCredit = PaymentMethod::findOne(['id' => PaymentMethod::TYPE_APPLY_CREDIT]);
        $applyCredit->updateAttributes([
            'displayed' => 0
        ]);
    }

    public function down()
    {
        echo "m170829_044323_change_apply_credit cannot be reverted.\n";

        return false;
    }
}
