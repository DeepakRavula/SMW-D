<?php

namespace console\controllers;

use Yii;
use Carbon\Carbon;
use common\models\AutoRenewal;
use common\models\AutoRenewalLessons;
use common\models\AutoRenewalPaymentCycle;
use common\models\User;
use common\models\Lesson;
use common\models\Enrolment;
use yii\console\Controller;
use common\models\Course;
use common\models\CourseProgramRate;
use common\models\CourseSchedule;
use common\models\LessonConfirm;
use common\models\Location;
use common\models\TeacherAvailability;

class EnrolmentController extends Controller
{
    public $id;

    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            $actionID == 'delete' || 'set-lesson-due-date' ||'fix-enrolment-lessons' ? ['id'] : []
        );
    }

    public function actionAutoRenewal()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $priorDate = (new Carbon())->addDays(Enrolment::AUTO_RENEWAL_DAYS_FROM_END_DATE);
        $courses = Course::find()
            ->regular()
            ->confirmed()
            ->needToRenewal($priorDate)
            ->privateProgram()
            ->notDeleted()
            ->all();
        foreach ($courses as $course) {
            $autoRenewal = new AutoRenewal();
            $autoRenewal->renewEnrolment($course);           
    }
    }

    public function actionDelete()
    {
        $model = Enrolment::findOne($this->id);
        return $model->deleteWithTransactionalData();
    }

    public function actionFixEnrolmentLessons() 
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $courses = Course::find()
            ->regular()
            ->confirmed()
            ->location($this->id)
            ->privateProgram()
            ->notDeleted()
            ->all();
            $count = 0;
        if ($courses) {    
            foreach ($courses as $course) {
                if (count($course->courseSchedules) > 1) {
                $lesson1 = Lesson::find()
                        ->andWhere(['courseId' => $course->id])
                        ->andWhere(['<=', 'DATE(lesson.date)', Carbon::parse($course->recentCourseSchedule->startDate)->format('Y-m-d')])
                        ->orderBy(['lesson.id' => SORT_DESC])
                        ->one();
                $lesson2 = Lesson::find()
                ->andWhere(['courseId' => $course->id])
                ->andWhere(['>=', 'DATE(lesson.date)', Carbon::parse($course->recentCourseSchedule->startDate)->format('Y-m-d')])
                ->orderBy(['lesson.id' => SORT_ASC])
                ->one();   
                print_r("\nCourse:".$course->id."Lesson 1:".$lesson1->id."Teacher id:".$lesson1->teacherId."Lesson 2:".$lesson2->id."Teacher id:".$lesson2->teacherId)
                
                } 
            }
        }

    }
}
