<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\Invoice;
use common\models\Enrolment;
use common\models\Lesson;
use common\models\Payment;
use common\models\CreditUsage;
use common\models\PaymentMethod;

class InvoiceController extends Controller
{

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
            ->unInvoicedProForma()
            ->scheduled()
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
		$lessons = Lesson::find()
            ->notDeleted()
            ->completedUnInvoiced()
            ->all();
		foreach($lessons as $lesson) {
			$lesson->createInvoice();
		}
		
        return true;
	}

	public function actionAllExpiredLessons()
	{
		$lessons = Lesson::find()
            ->notDeleted()
			->unscheduled()
			->notRescheduled()
			->expired()
            ->all();
		foreach($lessons as $lesson) {
			$lesson->createInvoice();
		}
		
        return true;
	}
}