<?php

use yii\db\Migration;
use common\models\Payment;
use common\models\InvoicePayment;
/**
 * Class m180927_145453_delete_invoice_payment
 */
class m180927_145453_delete_invoice_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $invoicePayments = InvoicePayment::find()
                ->andWhere(['id' => [1998,1999]])
                ->all();
                foreach($invoicePayments as $invoicePayment) {
                    $invoicePayment->delete();

                }
        // $payments = Payment::find()
        //     ->andWhere(['id' => [7847,7587]])
        //     ->all();
        //     foreach ($payments as $payment) {
        //         if ($payment->delete()) {
        //             print_r("cnvnvnvnvn");die;
        //         } else {
        //             echo "hihihi";
        //         }
        //     }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180927_145453_delete_invoice_payment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180927_145453_delete_invoice_payment cannot be reverted.\n";

        return false;
    }
    */
}
