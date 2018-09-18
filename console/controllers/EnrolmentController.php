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
            $actionID == 'delete' ? ['id'] : []
        );
    }
    
    public function actionAutoRenewal()
    {
        $priorDate = (new Carbon())->addDays(Enrolment::AUTO_RENEWAL_DAYS_FROM_END_DATE);
        $courses = Course::find()
                ->regular()
                ->confirmed()
                ->needToRenewal($priorDate)
                ->privateProgram()
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
}