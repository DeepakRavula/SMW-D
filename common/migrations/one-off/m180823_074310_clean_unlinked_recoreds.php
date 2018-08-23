<?php

use yii\db\Migration;
use common\models\InvoiceItemLesson;
use common\models\InvoiceItemPaymentCycleLesson;
use common\models\discount\InvoiceLineItemDiscount;
use common\models\PrivateLesson;
use common\models\payment\ProformaPaymentFrequency;
use common\models\Transaction;

/**
 * Class m180823_074310_clean_unlinked_recoreds
 */
class m180823_074310_clean_unlinked_recoreds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $invoiceItemLessons = InvoiceItemLesson::find()
            ->joinWith('invoiceLineItem')
            ->andWhere(['invoice_line_item.id' => null])
            ->all();
        foreach ($invoiceItemLessons as $invoiceItemLesson) {
            $invoiceItemLesson->delete();
        }

        $invoicePyCyLessons = InvoiceItemPaymentCycleLesson::find()
            ->joinWith('invoiceLineItem')
            ->andWhere(['invoice_line_item.id' => null])
            ->all();
        foreach ($invoicePyCyLessons as $invoicePyCyLesson) {
            $invoicePyCyLesson->delete();
        }

        $invoiceLiItDiscounts = InvoiceLineItemDiscount::find()
            ->joinWith('invoiceLineItem')
            ->andWhere(['invoice_line_item.id' => null])
            ->all();
        foreach ($invoiceLiItDiscounts as $invoiceLiItDiscount) {
            $invoiceLiItDiscount->delete();
        }

        $privatelessons = PrivateLesson::find()
            ->joinWith('lesson')
            ->andWhere(['lesson.id' => null])
            ->all();
        foreach ($privatelessons as $privatelesson) {
            $privatelesson->delete();
        }

        $proformaPaymentFrequices = ProformaPaymentFrequency::find()
            ->joinWith('invoice')
            ->andWhere(['invoice.id' => null])
            ->all();
        foreach ($proformaPaymentFrequices as $proformaPaymentFrequency) {
            $proformaPaymentFrequency->delete();
        }

        $transactions = Transaction::find()
            ->joinWith('invoice')
            ->joinWith('payment')
            ->andWhere(['AND', ['invoice.id' => null], ['payment.id' => null]])
            ->all();
        foreach ($transactions as $transaction) {
            $transaction->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180823_074310_clean_unlinked_recoreds cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180823_074310_clean_unlinked_recoreds cannot be reverted.\n";

        return false;
    }
    */
}
