<?php

use yii\db\Migration;
use common\models\CourseSchedule;

/**
 * Class m180905_061830_adding_teacher_of_course_in_course_Schedule
 */
class m180905_061830_adding_teacher_of_course_in_course_Schedule extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $courseSchedules = CourseSchedule::find()->all();
        foreach($courseSchedules as $courseSchedule) {
        $courseSchedule->updateAttributes([
            'teacherId' => $courseSchedule->course->teacherId,
        ]);
        }


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180905_061830_adding_teacher_of_course_in_course_Schedule cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180905_061830_adding_teacher_of_course_in_course_Schedule cannot be reverted.\n";

        return false;
    }
    */
}
