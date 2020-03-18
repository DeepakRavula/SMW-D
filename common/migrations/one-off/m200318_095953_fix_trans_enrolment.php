<?php

use common\models\Enrolment;
use common\models\Lesson;
use yii\db\Migration;

/**
 * Class m200318_095953_fix_trans_enrolment
 */
class m200318_095953_fix_trans_enrolment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $enrolment = Enrolment::findOne(3191);
        $lessons = Lesson::find()
                    ->andWhere(['lesson.courseId' => $enrolment->course->id])
                    ->andWhere(['>', 'lesson.id', 1008532])
                    ->all();
        foreach ($lessons as $lesson) {
            $lesson->updateAttributes(['teacherId' => 3883]);
        }

        $recentCourseSchedule = $enrolment->course->recentCourseSchedule;
        $recentCourseSchedule->updateAttributes(['teacherId' => 3883]);
        $enrolment->course->updateAttributes(['teacherId' => 3883]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200318_095953_fix_trans_enrolment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200318_095953_fix_trans_enrolment cannot be reverted.\n";

        return false;
    }
    */
}
