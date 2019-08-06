<?php

namespace console\controllers;

use Yii;
use Carbon\Carbon;
use common\models\User;
use common\models\Enrolment;
use yii\console\Controller;
use common\models\Course;
use common\models\CourseProgramRate;
use common\models\Location;
use yii\helpers\Console;
use common\models\Lesson;

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
        return array_merge(parent::options($actionID),
            $actionID == 'delete' || 'set-lesson-due-date' ? ['id'] : []
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
            $lastPaymentCycle = $course->enrolment->lastPaymentCycle;
            $lastPaymentCycleStartDate = new Carbon($lastPaymentCycle->startDate);
            $lastPaymentCycleEndDate = new Carbon($lastPaymentCycle->endDate);
            $lastPaymentCycleMonthCount = $lastPaymentCycleEndDate->diffInMonths($lastPaymentCycleStartDate);
            $renewalStartDate = (new Carbon($course->endDate))->addDays(1);
            $renewalEndDate = (new Carbon($course->endDate))->addMonths(11)->endOfMonth();
            $courseProgramRate = new CourseProgramRate();
            $courseProgramRate->courseId = $course->id;
            $courseProgramRate->startDate  = $renewalStartDate->format('Y-m-d');
            $courseProgramRate->endDate = $renewalEndDate->format('Y-m-d');
            $courseProgramRate->programRate = $course->program->rate;
            $courseProgramRate->save();
            $course->updateAttributes(['endDate' => $renewalEndDate]);
            $interval = new \DateInterval('P1D');
            $start = new \DateTime($renewalStartDate);            
            $end = new \DateTime($renewalEndDate);
            $period = new \DatePeriod($start, $interval, $end);
            foreach ($period as $day) {
                $checkDay = (int) $day->format('N') === (int) $course->recentCourseSchedule->day;
                if ($checkDay) {
                    if ($course->isProfessionalDevelopmentDay($day)) {
                        continue;
                    }
                    $isConfirmed = true;
                    $course->createLesson($day, $isConfirmed);
                }
            }
            if ($lastPaymentCycleMonthCount !== $course->enrolment->paymentsFrequency->frequencyLength) {
                $lastPaymentCycle->endDate = $lastPaymentCycleStartDate
                        ->addMonth($course->enrolment->paymentsFrequency->frequencyLength)
                        ->subDay(1);
                $lastPaymentCycle->save();
                $nextPaymentCycleStartDate = (new Carbon($lastPaymentCycle->endDate))->addDay(1);
                $course->enrolment->setPaymentCycle($nextPaymentCycleStartDate);
            } else {
                $course->enrolment->setPaymentCycle($renewalStartDate);
            }
        }
    }

    public function actionDelete()
    {
        $model = Enrolment::findOne($this->id);
        return $model->deleteWithTransactionalData();
    }

    public function actionSetLessonDueDate()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        Console::startProgress(0, 'Processing Enrolments');
        $enrolments = Enrolment::find()
                       ->location($this->id) 
                       ->isConfirmed()
                       ->notDeleted()
                       ->privateProgram()
                       ->all();
        foreach ($enrolments as $enrolment){
            Console::output("processing: " . $enrolment->id . 'processing', Console::FG_GREEN, Console::BOLD);
           $this->setDueDate($enrolment->id);
        }  
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);             
        return true;
    }

    public function setDueDate($enrolmentId)
    {
        $lessons = Lesson::find()
            ->enrolment($enrolmentId)
            ->isConfirmed()
            ->notCanceled()
            ->notDeleted()
            ->all();
        foreach ($lessons as $lesson){
            if ($lesson->paymentCycle) {
                $dueDateMonth = Carbon::parse($lesson->dueDate)->format('m');
                $dueDateYear = Carbon::parse($lesson->dueDate)->format('Y');
                $formatedDate = 15 . '-' . $dueDateMonth . '-' . $dueDateYear;
                $dueDate = Carbon::parse($formatedDate)->format('Y-m-d');
                $lesson->updateAttributes(['dueDate' => $dueDate]); 
            }
        }
        return true;
    }
}