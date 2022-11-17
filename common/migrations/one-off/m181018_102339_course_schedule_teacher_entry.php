<?php

use yii\db\Migration;
use common\models\CourseSchedule;

/**
 * Class m181018_102339_course_schedule_teacher_entry
 */
class m181018_102339_course_schedule_teacher_entry extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $courseSchedule = CourseSchedule::findOne(['id' => 1470]);
        $courseSchedule->endDate = $courseSchedule->course->endDate;
        $courseSchedule->teacherId = $courseSchedule->course->teacherId;
        $courseSchedule->save();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181018_102339_course_schedule_teacher_entry cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181018_102339_course_schedule_teacher_entry cannot be reverted.\n";

        return false;
    }
    */
}
