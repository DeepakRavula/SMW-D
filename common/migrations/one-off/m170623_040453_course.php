<?php

use yii\db\Migration;
use common\models\Course;
use common\models\CourseSchedule;

class m170623_040453_course extends Migration
{
    public function up()
    {
        foreach (Course::find()->all() as $course) {
            if (empty($course->courseSchedules)) {
                $this->insert('course_schedule', [
                    'courseId' => $course->id,
                    'day' => $course->day,
                    'duration' => $course->duration,
                    'fromTime' => $course->fromTime,
                ]);
            }
        }
        $this->dropColumn('course', 'day');
        $this->dropColumn('course', 'fromTime');
        $this->dropColumn('course', 'duration');
    }

    public function down()
    {
        echo "m170623_040453_course cannot be reverted.\n";

        return false;
    }
}
