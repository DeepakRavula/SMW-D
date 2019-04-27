<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Enrolment;
use common\models\Payment;
use common\models\User;
use common\models\Lesson;
use common\models\LessonPayment;
use common\models\Location;
use common\models\CustomerRecurringPayment;
use common\models\CustomerRecurringPaymentEnrolment;
use common\models\RecurringPayment;
use Carbon\Carbon;
use yii\helpers\Console;

class RecurringPaymentController extends Controller
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
        $locationIds = [];
        $locations = Location::find()->notDeleted()->cronEnabledLocations()->all();
        foreach ($locations as $location) {
            $locationIds[] = $location->id;
        }
        $currentDate = (new \DateTime())->format('Y-m-d');
        $recurringPayments = CustomerRecurringPayment::find()
                                ->notDeleted()
                                ->andWhere(['nextEntryDay' => Carbon::parse($currentDate)->format('Y-m-d')])
                                ->andWhere(['>=', 'DATE(customer_recurring_payment.expiryDate)', $currentDate])
                                ->isRecurringPaymentEnabled()
                                ->andWhere(['>', 'amount' ,0.00])
                                ->joinWith(['customer' =>function ($query) { 
                                    $query->notDeleted();
                                }])
                                ->andWhere(['>=', 'DATE(customer_recurring_payment.startDate)', $currentDate])
                                ->all();
        $count = count($recurringPayments);                        
        Console::startProgress(0, $count, 'Processing Recurring Payments.....');                        
        foreach ($recurringPayments as $recurringPayment) {
            $frequencyDays = $recurringPayment->paymentFrequencyId * 30;
            $startDate = Carbon::parse($currentDate)->modify('-'.$frequencyDays.'days')->format('Y-m-d');
            $endDate = Carbon::parse($currentDate)->format('Y-m-d');
            Console::output("processing for " . $recurringPayment->customer->publicIdentity, Console::FG_GREEN, Console::BOLD);
            $previousRecordedPayment = RecurringPayment::find()
                                        ->andWhere(['customerRecurringPaymentId' => $recurringPayment->id])
                                        ->between($startDate,$endDate)
                                        ->all();
            if (!$previousRecordedPayment) {
            $payment = new Payment();
            $payment->amount = $recurringPayment->amount;
            $day = $recurringPayment->paymentDay;
            if ($day > $recurringPayment->entryDay) {
                $month = Carbon::parse($recurringPayment->nextEntryDay)->addMonths(1)->format('m');
            } else {
                $month = Carbon::parse($recurringPayment->nextEntryDay)->format('m');  
            }
            $year = Carbon::parse($currentDate)->format('Y');
            $formatedDate = $day . '-' . $month . '-' . $year;
            $date = (new \DateTime($formatedDate))->format('Y-m-d H:i:s');
            $payment->date = $date;
            $payment->user_id = $recurringPayment->customerId;
            $payment->payment_method_id = $recurringPayment->paymentMethodId;
            $payment->save();
            $recurringPaymentModel = new RecurringPayment();
            $recurringPaymentModel->paymentId = $payment->id;
            $recurringPaymentModel->customerRecurringPaymentId = $recurringPayment->id;
            $recurringPaymentModel->date = Carbon::parse($currentDate)->format('Y-m-d');
            $recurringPaymentModel->save();
            $customerRecurringPaymentModel = CustomerRecurringPayment::findOne($recurringPayment->id);
            $customerRecurringPaymentModel->nextEntryDay = Carbon::parse($customerRecurringPaymentModel->nextEntryDay)->addMonthsNoOverflow($customerRecurringPaymentModel->paymentFrequencyId)->format('Y-m-d');
            $customerRecurringPaymentModel->save();
            $recurringPaymentEnrolments = $recurringPayment->enrolments;
            $paymentAmount = $payment->amount;
            foreach ($recurringPaymentEnrolments as $enrolment) {
                $invoicedLessons = Lesson::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->notCanceled()
                    ->enrolment($enrolment->id)
                    ->invoiced();
                $query = Lesson::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->notCanceled()
                    ->dueUntil($date)
                    ->enrolment($enrolment->id)
                    ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'lesson.id = invoiced_lesson.id')
                    ->andWhere(['invoiced_lesson.id' => null])
                    ->orderBy(['lesson.date' => SORT_ASC]);
                $lessonsToPay = $query->all();
                foreach ($lessonsToPay as $lesson) {
                    if ($paymentAmount > 0) {
                    $lessonPayment = new LessonPayment();
                    $lessonPayment->enrolmentId = $enrolment->id;
                    $lessonPayment->paymentId = $payment->id;
                    $lessonPayment->lessonId = $lesson->id;
                    if ($paymentAmount < round($lesson->getOwingAmount($enrolment->id), 2) ) {
                        $lessonPayment->amount = $paymentAmount;
                    } else {
                        $lessonPayment->amount = round($lesson->getOwingAmount($enrolment->id), 2);
                    }
                    $paymentAmount = $paymentAmount - $lessonPayment->amount;
                    $lessonPayment->save();
                  
                }
            }
            }
        }
    }
        return true;
    }
}

