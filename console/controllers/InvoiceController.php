<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\Invoice;
use common\models\Enrolment;
use common\models\Lesson;

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
            ->unInvoiced()
            ->scheduled()
            ->between($priorDate, $priorDate)
            ->all();
        $monthFirstDate        = \DateTime::createFromFormat('Y-m-d',
                $priorDate->format('Y-m-1'));
        $monthLastDate         = \DateTime::createFromFormat('Y-m-d',
                $priorDate->format('Y-m-t'));
        if (!empty($matchedLessons)) {
            foreach ($matchedLessons as $matchedLesson) {
                if (!$matchedLesson->isFirstLessonDate($priorDate) || !$matchedLesson->enrolment->isMonthlyPaymentFrequency()) {
                    continue;
                }
                $lessons             = Lesson::find()
                        ->where(['courseId' => $matchedLesson->courseId])
                        ->scheduled()
                        ->between($monthFirstDate, $monthLastDate)
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
}