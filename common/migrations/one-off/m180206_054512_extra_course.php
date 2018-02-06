<?php

use yii\db\Migration;
use common\models\Course;

/**
 * Class m180206_054512_extra_course
 */
class m180206_054512_extra_course extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $privateCourses = Course::find()
                ->confirmed()
                ->privateProgram()
                ->all();
        foreach ($privateCourses as $privateCourse) {
            if (count($privateCourse->enrolments) > 1) {
                foreach ($privateCourse->enrolments as $enrolment) {
                    if ($enrolment->isExtra()) {
                        $course = clone $privateCourse;
                        $course->id = null;
                        $course->isNewRecord = true;
                        $course->type = Course::TYPE_EXTRA;
                        $course->save();
                        $enrolment->updateAttributes([
                            'courseId' => $course->id
                        ]);
                        foreach ($privateCourse->extraLessons as $extraLesson) {
                            $extraLesson->updateAttributes(['courseId' => $course->id]);
                        }
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180206_054512_extra_course cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180206_054512_extra_course cannot be reverted.\n";

        return false;
    }
    */
}
