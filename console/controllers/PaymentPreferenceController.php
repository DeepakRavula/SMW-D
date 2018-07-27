<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Invoice;
use common\models\User;
use common\models\Lesson;

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
                $dateRange = $enrolment->getPaymentCycleDateRange(null, $currentDate);
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
                if ($lessonIds) {
                    $lessonsToPay = Lesson::find()
                        ->andWhere(['id' => $lessonIds])
                        ->all();
                    foreach ($lessonsToPay as $lesson) {
                        $lessonPayment = new LessonPayment();
                        $lessonPayment->enrolmentId = $enrolment->id;
                        $lessonPayment->paymentId = $enrolment->id;
                        $lessonPayment->lessonId = $lesson->id;
                        $lessonPayment->save();
                    }
                }
            }
        }
        
        return true;
    }
}