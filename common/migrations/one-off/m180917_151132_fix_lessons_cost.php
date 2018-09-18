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
        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->location([14,15])
            ->notCanceled()
            ->all();
            foreach ($lessons as $lesson) {
                if ($lesson->hasRootLesson()) {
                    if ($lesson->teacherId != $lesson->rootLesson->teacherId) {
                        $qualification = Qualification::findOne(['teacher_id' => $lesson->teacherId,
                        'program_id' => $lesson->course->program->id]);
                        $lesson->updateAttributes(['teacherRate' => $qualification->rate ?? 0]);
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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180917_151132_fix_lessons_cost cannot be reverted.\n";

        return false;
    }
    */
}
