<?php

use yii\db\Migration;
use common\models\InvoiceItemPaymentCycleLesson;

class m170820_110736_root_lesson_mapping extends Migration
{
    public function up()
    {
        $invoiceItems = InvoiceItemPaymentCycleLesson::find()
                ->all();
        foreach ($invoiceItems as $invoiceItem) {
            if ($invoiceItem->lesson->rootLesson) {
                $invoiceItem->paymentCycleLessonId = $invoiceItem->lesson->rootLesson
                        ->paymentCycleLesson->id;
                $invoiceItem->save();
            }
        }
    }

    public function down()
    {
        echo "m170820_110736_root_lesson_mapping cannot be reverted.\n";

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
