<?php

namespace common\models;

use Yii;
/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $type
 * @property string $amount
 * @property string $date
 * @property int $status
 */
trait Payable
{
    public function makeInvoicePayment($lesson)
    {
        if($lesson->canInvoice()) {
            if (!$lesson->hasInvoice()) {
                $invoice = $lesson->createPrivateLessonInvoice();
            } else if (!$lesson->invoice->isPaid()) {
                if ($lesson->hasLessonCredit($lesson->enrolment->id)) {
                    $netPrice = $lesson->getLessonCreditAmount($lesson->enrolment->id);
                    if ($lesson->isExploded) {
                        $netPrice = $lesson->getSplitedAmount() - $lesson->
                                getCreditUsedAmount($lesson->enrolment->id);
                    }
                    $lesson->invoice->addLessonDebitPayment($lesson, $netPrice, $lesson->enrolment);
                }
            }
        }
    }

    public function makeGroupInvoicePayment($lesson, $enrolment)
    {
        if($lesson->canInvoice()) {
            if (!$enrolment->hasInvoice($lesson->id)) {
                $invoice = $lesson->createGroupInvoice($enrolment->id);
            } else if (!$enrolment->getInvoice($lesson->id)->isPaid()) {
                if ($lesson->hasLessonCredit($enrolment->id)) {
                    $netPrice = $lesson->getLessonCreditAmount($enrolment->id);
                    $enrolment->getInvoice($lesson->id)->addLessonDebitPayment($lesson, $netPrice, $enrolment);
                }
            }
        }
    }
    
    public function createCreditUsage($creditPaymentId, $debitPaymentId)
    {
            $creditUsageModel = new CreditUsage();
            $creditUsageModel->credit_payment_id = $creditPaymentId;
            $creditUsageModel->debit_payment_id = $debitPaymentId;
            $creditUsageModel->save();	
    }
    
    public function addLessonDebitPayment($lesson, $amount, $enrolment)
    {
        $paymentModel = new Payment();
        $paymentModel->amount = $amount;
        $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
        $paymentModel->invoiceId = $this->id;
        $paymentModel->reference = $lesson->id;
        $paymentModel->save();
		
        $creditPaymentId = $paymentModel->id;
        $paymentModel->id = null;
        $paymentModel->isNewRecord = true;
        $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
        $paymentModel->lessonId = $lesson->id;
        $paymentModel->invoiceId = null;
        $paymentModel->reference = $this->id;
        $paymentModel->save();

        $debitPaymentId = $paymentModel->id;
        $lesson->addLessonPayment($debitPaymentId, $enrolment->id);
        $this->createCreditUsage($creditPaymentId, $debitPaymentId);
    }
    
    public function addInvoicePayment($invoice, $amount)
    {
        $paymentModel = new Payment();
        $paymentModel->amount = $amount;
        $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
        $paymentModel->invoiceId = $this->id;
        $paymentModel->reference = $invoice->id;
        $paymentModel->save();
		
        $creditPaymentId = $paymentModel->id;
        $paymentModel->id = null;
        $paymentModel->isNewRecord = true;
        $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
        $paymentModel->invoiceId = $invoice->id;
        $paymentModel->reference = $this->id;
        $paymentModel->save();

        $debitPaymentId = $paymentModel->id;
        $this->createCreditUsage($creditPaymentId, $debitPaymentId);
    }

    public function addGroupLessonCredit()
    {
        $enrolment = Enrolment::findOne($this->lineItem->lineItemEnrolment->enrolmentId);
        $lessons = Lesson::find()
                        ->isConfirmed()
                        ->notDeleted()
                        ->joinWith('enrolment')
                        ->andWhere(['enrolment.id' => $enrolment->id])
                        ->all();
        $courseCount = $enrolment->courseCount;
        foreach($lessons as $lesson) {
            $amount = $enrolment->proFormaInvoice->netSubtotal / $courseCount - 
                    $lesson->getCreditAppliedAmount($enrolment->id);
            if ($amount > $this->proFormaCredit) {
                $amount = $this->proFormaCredit;
            }
            if ($this->hasProFormaCredit() && !empty($amount)) {
                $lesson->createLessonCreditPayment($this, $amount, $enrolment);
                $this->makeGroupInvoicePayment($lesson, $enrolment);
            }
        }
    }
    
    public function addPrivateLessonCredit()
    {
        foreach ($this->lineItems as $lineItem) {
            $lesson = Lesson::find()
                        ->descendantsOf($lineItem->proFormaLesson->id)
                        ->orderBy(['id' => SORT_DESC])
                        ->one();
            if (!$lesson) {
                $lesson = Lesson::findOne($lineItem->proFormaLesson->id);
            }
            if ($lesson->isExploded) {
                $parentLesson = $lesson->parent()->one();
                $splitLessons = Lesson::find()
                        ->descendantsOf($parentLesson->id)
                        ->orderBy(['id' => SORT_DESC])
                        ->all();
                foreach ($splitLessons as $splitLesson) {
                    $amount = $splitLesson->getSplitedAmount() - $splitLesson->getCreditAppliedAmount($splitLesson->enrolment->id);
                    if ($amount > $this->proFormaCredit) {
                        $amount = $this->proFormaCredit;
                    }
                    if ($this->hasProFormaCredit() && !empty($amount)) {
                        $splitLesson->createLessonCreditPayment($this, $amount);
                        $this->makeInvoicePayment($splitLesson);
                    }
                }
            } else {
                if ($this->isExtraLessonProformaInvoice()) {
                    $lesson = Lesson::findOne($this->lineItem->lesson->id);
                }
                $amount = $lesson->proFormaLineItem->netPrice - $lesson->getCreditAppliedAmount($lesson->enrolment->id);
                if ($amount > $this->proFormaCredit) {
                    $amount = $this->proFormaCredit;
                }
                if ($this->hasProFormaCredit() && !empty($amount)) {
                    $lesson->createLessonCreditPayment($this, $amount);
                    $this->makeInvoicePayment($lesson);
                }
            }
        }
    }

    public function addLessonCredit()
    {
        if ($this->lineItem->isGroupLesson()) {
            $this->addGroupLessonCredit();
        } else {
            $this->addPrivateLessonCredit();
        }
    }
}
