<?php

use yii\db\Migration;
use common\models\Invoice;

/**
 * Class m180314_104711_update_payment_customer
 */
class m180314_104711_update_payment_customer extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $invoices = Invoice::find()
            ->all();
        foreach ($invoices as $model) {
            if ($model->allPayments && !empty($model->user_id)) {
                foreach ($model->allPayments as $payment) {
                    $payment->updateAttributes([
                        'user_id' => $model->user_id
                    ]);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180314_104711_update_payment_customer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180314_104711_update_payment_customer cannot be reverted.\n";

        return false;
    }
    */
}
