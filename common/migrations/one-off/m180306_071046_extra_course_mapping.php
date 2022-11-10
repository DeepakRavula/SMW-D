<?php

use yii\db\Migration;
use common\models\Course;
use common\models\Enrolment;

/**
 * Class m180306_071046_extra_course_mapping
 */
class m180306_071046_extra_course_mapping extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $courses = Course::find()
            ->privateProgram()
            ->extra()
            ->confirmed()
            ->all();
        foreach ($courses as $course) {
            $studentId = $course->enrolment->studentId;
            $programId = $course->programId;
            $privateCourse = Course::find()
                ->program($programId)
                ->privateProgram()
                ->regular()
                ->confirmed()
                ->student($studentId)
                ->one();
            if ($privateCourse) {
                $privateCourse->extendTo($course);
            }
        }
        $enrolments = Enrolment::find()
                ->notDeleted()
                ->isConfirmed()
                ->joinWith(['course' => function ($query) {
                    $query->extra();
                }])
                ->all();
        foreach ($enrolments as $enrolment) {
            $enrolment->updateAttributes(['isAutoRenew' => false]);
        }
        $groupEnrolments = Enrolment::find()
            ->isConfirmed()
            ->extra()
            ->notDeleted()
            ->all();
        foreach ($groupEnrolments as $groupEnrolment) {
            $groupEnrolment->updateAttributes(['isAutoRenew' => false]);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180306_071046_extra_course_mapping cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180306_071046_extra_course_mapping cannot be reverted.\n";

        return false;
    }
    */
}
