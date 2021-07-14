<?php

use common\models\Course;
use yii\db\Migration;

/**
 * Class m210714_062858_add_is_recent_course_schedule
 */
class m210714_062858_add_is_recent_course_schedule extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('course_schedule', 'isRecent',  $this->boolean()->null());
        $courses = Course::find()->all();
        foreach($courses as $course) {
            $recentCourseSchedule = $course->recentCourseSchedule;
            if ($recentCourseSchedule) {
            print_r("\n".$recentCourseSchedule->id);
            $recentCourseSchedule->isRecent = true;
            $recentCourseSchedule->save();
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210714_062858_add_is_recent_course_schedule cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210714_062858_add_is_recent_course_schedule cannot be reverted.\n";

        return false;
    }
    */
}
