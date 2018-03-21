<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\InvoiceItemPaymentCycleLesson;

/**
 * Class m180320_092141_root_lesson_mapping_issue
 */
class m180320_092141_root_lesson_mapping_issue extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $iipcls = InvoiceItemPaymentCycleLesson::find()
            ->all();
        foreach ($iipcls as $iipcl) {
            if (!$lesson = $iipcl->paymentCycleLesson) {
                continue;
            }
            $lesson = $iipcl->paymentCycleLesson->lesson;
            $leafLesson = Lesson::find()
                ->descendantsOf($lesson->id)
                ->orderBy(['lesson.id' => SORT_DESC])
                ->one();
            if ($leafLesson) {
                $iipcl->paymentCycleLessonId = $leafLesson->paymentCycleLesson->id;
                $iipcl->save();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180320_092141_root_lesson_mapping_issue cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180320_092141_root_lesson_mapping_issue cannot be reverted.\n";

        return false;
    }
    */
}
