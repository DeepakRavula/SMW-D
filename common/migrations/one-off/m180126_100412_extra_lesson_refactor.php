<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\Enrolment;

class m180126_100412_extra_lesson_refactor extends Migration
{
    public function up()
    {
        $extraLessons = Lesson::find()
                ->extra()
                ->all();
        foreach ($extraLessons as $extraLesson) {
            if (!$extraLesson->enrolment) {
                $course = $extraLesson->course;
                $course->studentId = $course->enrolment->studentId;
                $enrolment = $course->createExtraLessonEnrolment();
            } else {
                $enrolment = $extraLesson->enrolment;
            }
            $studentId = $enrolment->studentId;
            $programId = $extraLesson->programId;
            $courseId = $extraLesson->courseId;
            $studentEnrolment = Enrolment::find()
                ->notDeleted()
                ->isConfirmed()
                ->isRegular()
                ->joinWith(['course' => function ($query) use ($programId, $courseId) {
                    $query->andWhere(['course.programId' => $programId])
                        ->andWhere(['NOT', ['course.id' => $courseId]]);
                }])
                ->andWhere(['enrolment.studentId' => $studentId])
                ->one();
            if ($studentEnrolment) {
                if ($extraLesson->course) {
                    $extraLesson->course->delete();
                }
                $extraLesson->updateAttributes(['courseId' => $studentEnrolment->couseId]);
            }
        }
    }

    public function down()
    {
        echo "m180126_100412_extra_lesson_refactor cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
