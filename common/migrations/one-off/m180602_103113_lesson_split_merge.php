<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\PaymentCycleLesson;
use common\models\InvoiceItemPaymentCycleLesson;

/**
 * Class m180602_103113_lesson_split_merge
 */
class m180602_103113_lesson_split_merge extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $lessons = Lesson::find()
            ->location([15, 14])
            ->isConfirmed()
            ->unmergedSplit()
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->hasRotLesson()) {
                $rootLesson = $lesson->rootLessson;
                if (!$lesson->hasPaymentCycleLesson()) {
                    $pclesson = new PaymentCycleLesson();
                    $pclesson->lessonId = $lesson->id;
                    $pclesson->paymentCycleId = $rootLessson->paymentCycle->id;
                    $pclesson->save();
                }
                if ($rootLesson->hasProformaInvoice()) {
                    $lipclesson = new InvoiceItemPaymentCycleLesson();
                    $lipclesson->invoiceLineItemId = $rootLesson->proformaLineItem->id;
                    $lipclesson->paymentCycleLessonId = $lesson->paymentCycleLesson->id;
                    $lipclesson->save();
                }
            }
            if ($rootLesson->hasCreditApplied($rootLesson->enrolment->id) && !$lesson->hasCreditApplied($lesson->enrolment->id)) {
                $leafs = Lesson::find()
                    ->descendantsOf($rootLesson->id)
                    ->all();
                foreach ($leafs as $leaf) {
                    if (!$leaf->hasCreditApplied($leaf->enrolment->id)) {
                        
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180602_103113_lesson_split_merge cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180602_103113_lesson_split_merge cannot be reverted.\n";

        return false;
    }
    */
}
