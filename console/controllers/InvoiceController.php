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
        $paymentCycleStartDate        = \DateTime::createFromFormat('Y-m-d',
                $priorDate->format('Y-m-1'));
        if (!empty($matchedLessons)) {
            foreach ($matchedLessons as $matchedLesson) {
                $paymentCycleEndDate = $matchedLesson->enrolment->getLastDateOfPaymentCycle();
                if(!$matchedLesson->isFirstLessonDate($paymentCycleStartDate, $paymentCycleEndDate)) {
                    continue;
                }
                $paymentCycleStartDate        = \DateTime::createFromFormat('Y-m-d',
                        $priorDate->format('Y-m-1'));
                $lessons             = Lesson::find()
                        ->where(['courseId' => $matchedLesson->courseId])
                        ->unInvoicedProForma()
                        ->scheduled()
                        ->between($paymentCycleStartDate, $paymentCycleEndDate)
						->notDeleted()
                        ->all();
                $invoice              = new Invoice();
                $invoice->type        = Invoice::TYPE_PRO_FORMA_INVOICE;
                $invoice->location_id = $matchedLesson->enrolment->course->locationId;
                $invoice->user_id     = $matchedLesson->enrolment->student->customer_id;
                $invoice->save();
                foreach ($lessons as $lesson) {
					$invoice->addLineItem($lesson);
                }
                $invoice->save();
                $invoice->on(Invoice::EVENT_GENERATE, $invoice->sendEmail());
            }
        }
        return true;
    }
	
	public function actionAllCompletedLessons()
	{
		$lessons = Lesson::find()
            ->completedUnInvoiced()
            ->notDeleted()
			->all();
		foreach($lessons as $lesson) {
			$lesson->createRealInvoice();
		}
		
        return true;
	}
}