<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\Qualification;

/**
 * Class m180917_151132_fix_lessons_cost
 */
class m180917_151132_fix_lessons_cost extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->location([14,15])
            ->notCanceled()
            ->all();
                foreach ($lessons as $lesson) {
                    if ($lesson->hasRootLesson()) {
                        if ($lesson->teacherId != $lesson->rootLesson->teacherId) {
                            if (($lesson->teacherRate != $lesson->teacherCost) && ($lesson->teacherCost > 5) && ($lesson->teacherRate > 5)) {
                                $lesson->updateAttributes(['teacherRate' => $lesson->teacherCost]);
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
        echo "m180917_151132_fix_lessons_cost cannot be reverted.\n";

        return false;
    }
}
