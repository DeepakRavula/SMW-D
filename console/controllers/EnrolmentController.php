<?php

namespace console\controllers;

use Yii;
use Carbon\Carbon;
use common\models\User;
use yii\console\Controller;
use common\models\Course;
use common\models\EnrolmentProgramRate;

class EnrolmentController extends Controller
{
    public function init() {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    
    public function actionAutoRenewal()
    {
        $priorDate = (new \DateTime())->modify('+90 day');
        $courses = Course::find()
                ->confirmed()
                ->needToRenewal($priorDate)
                ->privateProgram()
                ->all();
        foreach ($courses as $course) {
            $renewalStartDate = (new Carbon($course->endDate))->addDays(1);
            $renewalEndDate = (new Carbon($course->endDate))->addMonths(11)->endOfMonth();
            $enrolmentProgramRate = new EnrolmentProgramRate();
            $enrolmentProgramRate->enrolmentId = $course->enrolment->id;
            $enrolmentProgramRate->startDate  = $renewalStartDate->format('Y-m-d');
            $enrolmentProgramRate->endDate = $renewalEndDate->format('Y-m-d');
            $enrolmentProgramRate->programRate = $course->program->rate;
            $enrolmentProgramRate->save();
            $course->updateAttributes(['endDate' => $renewalEndDate]);
            $interval = new \DateInterval('P1D');
            $start = new \DateTime($renewalStartDate);            
            $end = new \DateTime($renewalEndDate);
            $period = new \DatePeriod($start, $interval, $end);
            foreach ($period as $day) {
                $checkDay = (int) $day->format('N') === (int) $course->courseSchedule->day;
                if ($checkDay) {
                    if ($course->isProfessionalDevelopmentDay($day)) {
                        continue;
                    }
                    $isConfirmed = true;
                    $course->createLesson($day, $isConfirmed);
                }
            }
            $course->enrolment->resetPaymentCycle();
        }
    }
}