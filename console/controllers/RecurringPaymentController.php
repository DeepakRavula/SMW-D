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
        $currentDate = (new \DateTime())->format('Y-m-d');
        $recurringPayments = CustomerRecurringPayment::find()
            ->notDeleted()
            ->andWhere(['nextEntryDay' => Carbon::parse($currentDate)->format('Y-m-d')])
            ->andWhere(['>=', 'DATE(customer_recurring_payment.expiryDate)', $currentDate])
            ->isRecurringPaymentEnabled()
            ->andWhere(['>', 'amount', 0.00])
            ->joinWith(['customer' => function ($query) {
                $query->notDeleted();
            }])
            ->andWhere(['<=', 'DATE(customer_recurring_payment.startDate)', $currentDate])
            ->all();
        $count = count($recurringPayments);
        Console::startProgress(0, $count, 'Processing Recurring Payments.....');
        foreach ($recurringPayments as $recurringPayment) {
            $startDate = Carbon::parse($currentDate)->subMonthsNoOverflow($recurringPayment->paymentFrequencyId - 1)->format('Y-m-1');
            $endDate = Carbon::parse($currentDate)->format('Y-m-d');
            Console::output("processing for " . $recurringPayment->customer->publicIdentity, Console::FG_GREEN, Console::BOLD);
            $previousRecordedPayment = RecurringPayment::find()
                ->andWhere(['customerRecurringPaymentId' => $recurringPayment->id])
                ->between($startDate, $endDate)
                ->all();
            if (!$previousRecordedPayment && Carbon::parse($recurringPayment->expiryDate) >= Carbon::parse($recurringPayment->nextPaymentDate())) {
                $payment = new Payment();
                $payment->amount = $recurringPayment->amount;
                $date = Carbon::parse($recurringPayment->nextPaymentDate())->format('Y-m-d');
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
                        ->orderBy(['lesson.dueDate' => SORT_ASC]);
                    $lessonsToPay = $query->all();
                    foreach ($lessonsToPay as $lesson) {
                        if ($paymentAmount > 0) {
                            $lessonPayment = new LessonPayment();
                            $lessonPayment->enrolmentId = $enrolment->id;
                            $lessonPayment->paymentId = $payment->id;
                            $lessonPayment->lessonId = $lesson->id;
                            if ($paymentAmount < round($lesson->getOwingAmount($enrolment->id), 2)) {
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

    public function actionMissingRecurringPayment()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $currentDate = Carbon::now();
        $recurringPayments = CustomerRecurringPayment::find()
            ->notDeleted()
            ->andWhere(['>=', 'DATE(customer_recurring_payment.nextEntryDay)', $currentDate->format('Y-m-1')])
            ->andWhere(['<=', 'DATE(customer_recurring_payment.nextEntryDay)', $currentDate->format('Y-m-d')])
            ->andWhere(['>=', 'DATE(customer_recurring_payment.expiryDate)', $currentDate->format('Y-m-d')])
            ->isRecurringPaymentEnabled()
            ->andWhere(['>', 'amount', 0.00])
            ->joinWith(['customer' => function ($query) {
                $query->notDeleted()
                    ->location([4, 9, 14, 15, 16, 17, 18, 19, 20, 21]);
            }])
            ->andWhere(['<=', 'DATE(customer_recurring_payment.startDate)', $currentDate->format('Y-m-d')])
            ->all();
        $count = count($recurringPayments);
        Console::startProgress(0, $count, 'Processing Recurring Payments.....');
        foreach ($recurringPayments as $recurringPayment) {
            $startDate = $currentDate->subMonthsNoOverflow($recurringPayment->paymentFrequencyId - 1)->format('Y-m-1');
            $endDate = $currentDate->format('Y-m-d');
            Console::output("processing for " . $recurringPayment->customer->publicIdentity, Console::FG_GREEN, Console::BOLD);
            $previousRecordedPayment = RecurringPayment::find()
                ->andWhere(['customerRecurringPaymentId' => $recurringPayment->id])
                ->between($startDate, $endDate)
                ->all();
            if (!$previousRecordedPayment) {
                $payment = new Payment();
                $payment->amount = $recurringPayment->amount;
                $date = Carbon::parse($recurringPayment->nextPaymentDate())->format('Y-m-d');
                $payment->date = $date;
                $payment->user_id = $recurringPayment->customerId;
                $payment->payment_method_id = $recurringPayment->paymentMethodId;
                $payment->save();
                $recurringPaymentModel = new RecurringPayment();
                $recurringPaymentModel->paymentId = $payment->id;
                $recurringPaymentModel->customerRecurringPaymentId = $recurringPayment->id;
                $recurringPaymentModel->date = $currentDate->format('Y-m-d');
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
                        ->orderBy(['lesson.dueDate' => SORT_ASC]);
                    $lessonsToPay = $query->all();
                    foreach ($lessonsToPay as $lesson) {
                        if ($paymentAmount > 0) {
                            $lessonPayment = new LessonPayment();
                            $lessonPayment->enrolmentId = $enrolment->id;
                            $lessonPayment->paymentId = $payment->id;
                            $lessonPayment->lessonId = $lesson->id;
                            if ($paymentAmount < round($lesson->getOwingAmount($enrolment->id), 2)) {
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

    public function actionChangeExpiryDate()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $recurringPaymentIds = [422, 447, 448, 481, 565, 642, 647, 1094];
        $customerRecurringPayments = CustomerRecurringPayment::find()
            ->notDeleted()
            ->andWhere(['IN', 'id', $recurringPaymentIds])
            ->all();
        foreach ($customerRecurringPayments as $customerRecurringPayment) {
            $customerRecurringPayment->updateAttributes(['expiryDate' => '2023-01-31']);
        }
        return true;
    }

    public function actionUpdateRecurringPayments()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $currentDate = Carbon::now();

        $customerRecurringPayments = CustomerRecurringPayment::find()
            ->notDeleted()
            ->andWhere(['<', 'DATE(customer_recurring_payment.nextEntryDay)', $currentDate->format('Y-m-d')])
            ->andWhere(['>=', 'DATE(customer_recurring_payment.expiryDate)', $currentDate->format('Y-m-d')])
            ->isRecurringPaymentEnabled()
            ->andWhere(['>', 'amount', 0.00])
            ->joinWith(['customer' => function ($query) {
                $query->notDeleted()
                    ->location([1, 4, 9, 14, 15, 16, 17, 18, 19, 20, 21, 22]);
            }])
            ->andWhere(['customer_recurring_payment.id' => 1246])
            ->all();  
        foreach ($customerRecurringPayments as $customerRecurringPayment) {
            $startDate = $currentDate->subMonthsNoOverflow($customerRecurringPayment->paymentFrequencyId - 1)->format('Y-m-1');
            $endDate = $currentDate->format('Y-m-d');
            $previousRecordedPayment = RecurringPayment::find()
                ->andWhere(['customerRecurringPaymentId' => $customerRecurringPayment->id])
                ->orderBy(['recurring_payment.date' => SORT_DESC]);
            $previousRecordedPaymentAny = $previousRecordedPayment->one();
            $recentRecordedPayment = $previousRecordedPayment->between($startDate, $endDate)->one();
            if (!$recentRecordedPayment) {
                if ($previousRecordedPaymentAny) {
                    $nextEntryDay = Carbon::parse($previousRecordedPaymentAny->date)->addMonthsNoOverflow($customerRecurringPayment->paymentFrequencyId)->format('Y-m-d');
                } else {
                    $nextEntryDayDate = Carbon::parse($customerRecurringPayment->startDate)->format('d');
                    $nextEntryDayMonth = $currentDate->format('m');
                    $nextEntryDayYear = $currentDate->format('Y');
                    $nextEntryDay = Carbon::parse($nextEntryDayYear . '-' . $nextEntryDayMonth . '-' . $nextEntryDayDate)->format('Y-m-d');
                }
                
                while (Carbon::parse($nextEntryDay)->format('Y-m-d') <= $currentDate->format('Y-m-d')) {
                    $nextEntryDay = Carbon::parse($nextEntryDay)->addMonthsNoOverflow($customerRecurringPayment->paymentFrequencyId)->format('Y-m-d');
                    print_r("\nssss".$nextEntryDay);
                }
                $customerRecurringPayment->nextEntryDay = $nextEntryDay;
                die('coming');
                print_r("\nProcessing Customer Recurring payment:".$customerRecurringPayment->id);
                $customerRecurringPayment->save();
            }
        }
        return true;
    }
}
