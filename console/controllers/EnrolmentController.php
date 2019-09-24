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
            $autoRenewal = new AutoRenewal();
            $autoRenewal->enrolmentId = $course->enrolment->id;
            $autoRenewal->enrolmentEndDateCurrent = $course->enrolment->endDateTime;
            $autoRenewal->enrolmentEndDateNew = $renewalEndDate;
            $autoRenewal->paymentFrequency = $course->enrolment->paymentFrequencyId;
            $autoRenewal->lastPaymentCycleStartDate = $course->enrolment->lastPaymentCycle->startDate;
            $autoRenewal->lastPaymentCycleEndDate = $course->enrolment->lastPaymentCycle->endDate;
            $autoRenewal->save();
            
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
                    $createdLesson = $course->createAutoRenewalLesson($day);
                    $createdLesson->makeAsRoot();
                    $createdLesson->setDiscount();
                    $autoRenewalLesson = new AutoRenewalLessons();
                    $autoRenewalLesson->autoRenewalId = $autoRenewal->id;
                    $autoRenewalLesson->lessonId = $createdLesson->id;
                    $autoRenewalLesson->save();
                }
            }
            
            if ($lastPaymentCycleMonthCount !== $course->enrolment->paymentsFrequency->frequencyLength) {
                $lastPaymentCycle->endDate = $lastPaymentCycleStartDate
                    ->addMonth($course->enrolment->paymentsFrequency->frequencyLength)
                    ->subDay(1);
                $lastPaymentCycle->save();
                $nextPaymentCycleStartDate = (new Carbon($lastPaymentCycle->endDate))->addDay(1);
                $course->enrolment->setAutoRenewalPaymentCycle($nextPaymentCycleStartDate);
            } else {
                $course->enrolment->setAutoRenewalPaymentCycle($renewalStartDate);
            } 
            $course->updateAttributes(['endDate' => $renewalEndDate]);
            $course->enrolment->updateAttributes(['endDateTime' => $renewalEndDate]);
            $autoRenewalLessons = AutoRenewalLessons::find()
                        ->andWhere(['autoRenewalId' => $autoRenewal->id])
                        ->all();
            foreach ($autoRenewalLessons as $autoRenewalLesson) {
                $autoRenewalPaymentCycle = new AutoRenewalPaymentCycle();
                $autoRenewalPaymentCycle->autoRenewalId = $autoRenewal->id;
                $autoRenewalPaymentCycle->paymentCycleId = $autoRenewalLesson->paymentCycleLesson->paymentCycleId;
                $autoRenewalPaymentCycle->save();
            }
        }
    }

    public function actionDelete()
    {
        $model = Enrolment::findOne($this->id);
        return $model->deleteWithTransactionalData();
    }
}
