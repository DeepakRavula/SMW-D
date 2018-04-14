<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\Invoice;
use common\models\User;
use common\models\Lesson;

class InvoiceController extends Controller
{

	public function init() {
        parent::init();
		$user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    public function actionGenerateInvoice()
    {
        /**
         * 1. Get the date 15 days prior from today
         * 2. Check if any unpaid lesson start on that day.
         * 3. If yes, get the associated enrolment's payment frequency (assume monthly for now)
         * 4. Generated invoice for all the lessons for that month
         * 5. Send invoice to that customer
         * 6. Set the invoice sent flag as true
         */
        $priorDate             = (new \DateTime())->modify('+15 day');
        $matchedLessons        = Lesson::find()
            ->privateLessons()
            ->regular()
            ->unInvoicedProForma()
            ->isConfirmed()
            ->notCanceled()
            ->between($priorDate, $priorDate)
            ->notDeleted()
            ->all();
        if (!empty($matchedLessons)) {
            foreach ($matchedLessons as $matchedLesson) {
                $invoice = $matchedLesson->paymentCycle->createProFormaInvoice();
                $invoice->on(Invoice::EVENT_GENERATE, $invoice->sendEmail());
            }
        }
        return true;
    }
	
    public function actionAllCompletedLessons()
    {
        $query = Lesson::find()
                ->isConfirmed()
                ->notDeleted();
        $privateLessons = $query->completedUnInvoicedPrivate()->all();
        $groupLessons = $query->groupLessons()->completed()->all();
        foreach ($groupLessons as $lesson) {
            foreach ($lesson->enrolments as $enrolment) {
                if (!$enrolment->hasInvoice($lesson->id)) {
                    $lesson->createGroupInvoice($enrolment->id);
                }
            }
        }
        foreach($privateLessons as $lesson) {
            $lessonDate = new \DateTime($lesson->date);
            $enrolmentDate = new \DateTime($lesson->enrolment->createdAt);
            if ($enrolmentDate < $lessonDate) {
                $lesson->createPrivateLessonInvoice();
            }
        }

        return true;
    }

    public function actionAllExpiredLessons()
    {
        $lessons = Lesson::find()
            ->privateLessons()
			->isConfirmed()
            ->notDeleted()
            ->unscheduled()
            ->notRescheduled()
            ->expired()
            ->all();
        try {
            foreach($lessons as $lesson) {
                $lesson->createPrivateLessonInvoice();
            }
        } catch (\Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
        }

        return true;
    }

    public function actionPaymentPreferenceInvoice()
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
            $invoices = Invoice::find()
                ->notDeleted()
                ->proFormaInvoice()
                ->customer($customer->id)
                ->unpaid()
                ->all();
            foreach ($invoices as $invoice) {
                $invoice->addPreferredPayment($customer->customerPaymentPreference->paymentMethodId);
            }
        }

        return true;
    }
}