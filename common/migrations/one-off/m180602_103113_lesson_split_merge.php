<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\PaymentCycleLesson;
use common\models\InvoiceItemPaymentCycleLesson;
use common\models\Invoice;

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
        $ids = [7235, 7236, 7237, 7238, 7240, 7241, 7744, 7745, 7913, 7914];
        $pfis = Invoice::find()
            ->andWhere(['id' => $ids])
            ->all();
        foreach ($pfis as $pfi) {
            $pfi->delete();
        }
        $lessons = Lesson::find()
            ->location([15, 14])
            ->isConfirmed()
            ->unmergedSplit()
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->hasRootLesson()) {
                $rootLesson = $lesson->rootLesson;
                if (!$lesson->hasPaymentCycleLesson()) {
                    $pclesson = new PaymentCycleLesson();
                    $pclesson->lessonId = $lesson->id;
                    $pclesson->paymentCycleId = $rootLesson->paymentCycle->id;
                    $pclesson->save();
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
