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
        $currentDate = new \DateTime();
        $customers = User::find()
                ->joinWith(['customerPaymentPreference' => function ($query) use ($currentDate) {
                    $query->date($currentDate)
                        ->notExpired();
                }])
                ->notDeleted()
                ->all();
        foreach ($customers as $customer) {
            $enrolments = Enrolment::find()
                ->notDeleted()
                ->isConfirmed()
                ->privateProgram()
                ->andWhere(['NOT', ['enrolment.paymentFrequencyId' => 0]])
                ->isRegular()
                ->customer($customer->id)
                ->joinWith(['course' => function ($query) use ($currentDate) {
                    $query->andWhere(['>=', 'DATE(course.endDate)', $currentDate->format('Y-m-d')])
                            ->confirmed();
                }])
                ->all();
            foreach ($enrolments as $enrolment) {
                $dateRange = $enrolment->getPaymentCycleDateRange(null, $currentDate->format('Y-m-d'));
                list($from_date, $to_date) = explode(' - ', $dateRange);
                $fromDate = new \DateTime($from_date);
                $toDate = new \DateTime($to_date);
                $invoicedLessons = Lesson::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->notCanceled()
                    ->privateLessons()
                    ->between($fromDate, $toDate)
                    ->enrolment($enrolment->id)
                    ->invoiced();
                $query = Lesson::find()   
                    ->notDeleted()
                    ->isConfirmed()
                    ->notCanceled()
                    ->privateLessons()
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
                    $payment->user_id = $customer->id;
                    $payment->payment_method_id = $customer->customerPaymentPreference->paymentMethodId;
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
        }
        
        return true;
    }
}