<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Enrolment;
use common\models\Payment;
use common\models\User;
use common\models\Lesson;
use common\models\LessonPayment;

class PaymentPreferenceController extends Controller
{
    public function init() 
    {
        parent::init();
		$user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function actionCreate()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $currentDate = new \DateTime();
        $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
        $enrolments = Enrolment::find()
            ->notDeleted()
            ->isConfirmed()
            ->privateProgram()
            ->andWhere(['NOT', ['enrolment.paymentFrequencyId' => 0]])
            ->isRegular()
            ->joinWith(['course' => function ($query) use ($priorDate) {
                $query->andWhere(['>=', 'DATE(course.endDate)', $priorDate])
                        ->location([14, 15, 16])
                        ->confirmed();
            }])
            ->paymentPrefered()
            ->all();
        foreach ($enrolments as $enrolment) {
            $dateRange = $enrolment->getCurrentPaymentCycleDateRange($priorDate);
            list($from_date, $to_date) = explode(' - ', $dateRange);
            $fromDate = new \DateTime($from_date);
            $toDate = new \DateTime($to_date);
            $invoicedLessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->enrolment($enrolment->id)
                ->invoiced();
            $query = Lesson::find()   
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->enrolment($enrolment->id)
                ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'lesson.id = invoiced_lesson.id')
                ->andWhere(['invoiced_lesson.id' => null])
                ->orderBy(['lesson.date' => SORT_ASC]);
            $unInvoicedLessons = $query->all();
            $owingLessonIds = [];
            $amount = 0;
            foreach ($unInvoicedLessons as $lesson) {
                if ($lesson->isOwing($enrolment->id)) {
                    $owingLessonIds[] = $lesson->id;
                    $amount += round($lesson->getOwingAmount($enrolment->id), 2);
                }
            }
            if ($owingLessonIds) {
                $payment = new Payment();
                $payment->amount = $amount;
                $day = $enrolment->customer->customerPaymentPreference->dayOfMonth;
                $currentPaymentCycleStartDate = new \DateTime($enrolment->currentPaymentCycle->startDate);
                $month = $currentPaymentCycleStartDate->format('m');
                $year = $currentPaymentCycleStartDate->format('Y');
                $formatedDate = $day . '-' . $month . '-' . $year;
                $date = (new \DateTime($formatedDate))->format('Y-m-d H:i:s');
                $payment->date = $date;
                $payment->user_id = $enrolment->customer->id;
                $payment->payment_method_id = $enrolment->customer->customerPaymentPreference->paymentMethodId;
                $payment->save();
                $lessonsToPay = Lesson::find()
                    ->andWhere(['id' => $owingLessonIds])
                    ->all();
                foreach ($lessonsToPay as $lesson) {
                    $lessonPayment = new LessonPayment();
                    $lessonPayment->enrolmentId = $enrolment->id;
                    $lessonPayment->paymentId = $payment->id;
                    $lessonPayment->lessonId = $lesson->id;
                    $lessonPayment->amount = round($lesson->getOwingAmount($enrolment->id), 2);
                    $lessonPayment->save();
                }
            }
        }
        
        return true;
    }
}