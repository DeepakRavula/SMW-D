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
        $monthFirstDate        = \DateTime::createFromFormat('Y-m-d',
                $priorDate->format('Y-m-1'));
        $monthLastDate         = \DateTime::createFromFormat('Y-m-d',
                $priorDate->format('Y-m-t'));
        $lessons               = Lesson::find()
            ->scheduledBetween($priorDate, $priorDate)
            ->all();
        $firstLessonsCourseIds = [];
        if (!empty($lessons)) {
            foreach ($lessons as $lesson) {
                if (!$lesson->isFirstLessonDate($priorDate) || !$lesson->enrolment->isMonthlyPaymentFrequency()) {
                    continue;
                }
                $firstLessonsCourseIds[] = $lesson->courseId;
            }
            if (!empty($firstLessonsCourseIds)) {
                foreach ($firstLessonsCourseIds as $firstLessonsCourseId) {
                    $enrolment           = Enrolment::findOne(['courseId' => $firstLessonsCourseId]);
                    $lessons             = Lesson::find()
                        ->where(['courseId' => $firstLessonsCourseId])
                        ->scheduledBetween($monthFirstDate, $monthLastDate)
                        ->all();
                    $invoice             = new Invoice();
                    $invoice->type       = Invoice::TYPE_PRO_FORMA_INVOICE;
                    $location_id         = $enrolment->course->locationId;
                    $lastProFormaInvoice = Invoice::lastProFormaInvoice($location_id);
                    if (empty($lastProFormaInvoice)) {
                        $invoiceNumber = 1;
                    } else {
                        $invoiceNumber = $lastProFormaInvoice->invoice_number + 1;
                    }
                    $invoice->user_id        = $enrolment->student->customer_id;
                    $invoice->invoice_number = $invoiceNumber;
                    $invoice->location_id    = $location_id;
                    $invoice->date           = (new \DateTime())->format('Y-m-d');
                    $invoice->status         = Invoice::STATUS_OWING;
                    $invoice->notes          = null;
                    $invoice->internal_notes = null;
                    $invoice->save();
                    $subTotal                = 0;
                    $taxAmount               = 0;
                    foreach ($lessons as $lesson) {
                        $lesson->bulkLessonsInvoiceLineItem($invoice);
                    }
                    $invoice           = Invoice::findOne(['id' => $invoice->id]);
                    $subTotal          = $invoice->getSubTotal();
                    $invoice->subTotal = $subTotal;
                    $totalAmount       = $subTotal + $taxAmount;
                    $invoice->tax      = $taxAmount;
                    $invoice->total    = $totalAmount;
                    $invoice->save();
                    $notify = $invoice->sendEmail();
                    if($notify)
                    {
                        echo 'Success';
                    } else{
                        echo 'Failed';
                    }
                }
            }
        }
    }
}