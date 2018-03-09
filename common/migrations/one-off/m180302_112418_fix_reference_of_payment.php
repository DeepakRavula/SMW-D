<?php

use yii\db\Migration;
use common\models\Payment;
use common\models\Invoice;

/**
 * Class m180302_112418_fix_reference_of_payment
 */
class m180302_112418_fix_reference_of_payment extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $payments = Payment::find()
            ->where(['payment_method_id' => [2, 3]])
            ->all();
        foreach ($payments as $payment) {
            if (is_numeric($payment->reference)) {
                $invoice = Invoice::findOne($payment->reference);
                $payment->updateAttributes(['reference' => $invoice->getInvoiceNumber()]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180302_112418_fix_reference_of_payment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180302_112418_fix_reference_of_payment cannot be reverted.\n";

        return false;
    }
    */
}
