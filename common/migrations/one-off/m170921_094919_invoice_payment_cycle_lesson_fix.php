<?php

use yii\db\Migration;
use common\models\InvoiceLineItem;
use common\models\Invoice;

class m170921_094919_invoice_payment_cycle_lesson_fix extends Migration
{
    public function up()
    {
        $pfiLineItems = InvoiceLineItem::find()
                ->joinWith('invoice')
                ->andWhere(['invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE,
                    'invoice.isDeleted' => false])
                ->all();
        foreach ($pfiLineItems as $pfiLineItem) {
            if ($pfiLineItem->lesson->rootLesson) {
                if ($pfiLineItem->lineItemPaymentCycleLesson->paymentCycleLessonId !==
                        $pfiLineItem->lesson->rootLesson->paymentCycleLesson->id) {
                    $pfiLineItem->lineItemPaymentCycleLesson->paymentCycleLessonId = $pfiLineItem
                            ->lesson->rootLesson->paymentCycleLesson->id;
                    $pfiLineItem->lineItemPaymentCycleLesson->save();
                }
            }
        }
    }

    public function down()
    {
        echo "m170921_094919_invoice_payment_cycle_lesson_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
