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
        if ($lesson->canInvoice()) {
            if (!$lesson->hasInvoice()) {
                $invoice = $lesson->createPrivateLessonInvoice();
            } elseif (!$lesson->invoice->isPaid()) {
                if ($lesson->hasLessonCredit($lesson->enrolment->id)) {
                    $netPrice = $lesson->getLessonCreditAmount($lesson->enrolment->id);
                    if ($lesson->isExploded) {
                        $netPrice = $lesson->getSplitedAmount() - $lesson->
                                getCreditUsedAmount($lesson->enrolment->id);
                    }
                    $lesson->invoice->addPayment($lesson, $netPrice, $lesson->enrolment);
                }
            }
        }
    }

    public function makeGroupInvoicePayment($lesson, $enrolment)
    {
        if ($lesson->canInvoice()) {
            if (!$enrolment->hasInvoice($lesson->id)) {
                $invoice = $lesson->createGroupInvoice($enrolment->id);
            } elseif (!$enrolment->getInvoice($lesson->id)->isPaid()) {
                if ($lesson->hasLessonCredit($enrolment->id)) {
                    $netPrice = $lesson->getLessonCreditAmount($enrolment->id);
                    $enrolment->getInvoice($lesson->id)->addPayment(
                        $lesson,
                        $netPrice,
                        $enrolment
                    );
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
    
    public function addPayment($from, $amount, $enrolment = null)
    {
        if (round(abs($amount), 2) < round(abs(0.00), 2)) {
            return true;
        }
        $paymentModel = new Payment();
        $paymentModel->amount = $amount;
        $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
        if ($this->tableName() === 'invoice') {
            $paymentModel->invoiceId = $this->id;
        } else {
            $paymentModel->lessonId = $this->id;
        }
        if ($from->tableName() === 'lesson') {
            $paymentModel->reference = $from->getLessonNumber();
        } else {
            $paymentModel->reference = $from->getInvoiceNumber();
        }
        $paymentModel->save();
        $creditPaymentId = $paymentModel->id;
        if ($this->tableName() === 'lesson') {
            if (!$enrolment) {
                $enrolment = $this->enrolment;
            }
            $this->addLessonPayment($creditPaymentId, $enrolment->id);
        }
        
        $paymentModel->id = null;
        $paymentModel->isNewRecord = true;
        $paymentModel->setScenario(Payment::SCENARIO_CREDIT_USED);
        $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
        if ($from->tableName() === 'invoice') {
            $paymentModel->invoiceId = $from->id;
            $paymentModel->lessonId = null;
        } else {
            $paymentModel->lessonId = $from->id;
            $paymentModel->invoiceId = null;
        }
        if ($this->tableName() === 'invoice') {
            $paymentModel->reference = $this->getInvoiceNumber();
        } else {
            $paymentModel->reference = $this->getLessonNumber();
        }
        $paymentModel->amount = -abs($paymentModel->amount);
        $paymentModel->save();

        $debitPaymentId = $paymentModel->id;
        if ($from->tableName() === 'lesson') {
            if (!$enrolment) {
                $enrolment = $from->enrolment;
            }
            $from->addLessonPayment($debitPaymentId, $enrolment->id);
        }
        $this->createCreditUsage($creditPaymentId, $debitPaymentId);
    }

    public function addGroupLessonCredit()
    {
        $enrolment = $this->lineItem->enrolment;
        $courseCount = count($enrolment->lessons);
        foreach ($this->lineItems as $lineItem) {
            $lesson = Lesson::find()
                        ->descendantsOf($lineItem->lesson->id)
                        ->orderBy(['id' => SORT_DESC])
                        ->one();
            if (!$lesson) {
                $lesson = Lesson::findOne($lineItem->lesson->id);
            }
            $amount = $enrolment->proFormaInvoice->netSubtotal / $courseCount -
                    $lesson->getCreditAppliedAmount($enrolment->id);
            if ($amount > $this->proFormaCredit) {
                $amount = $this->proFormaCredit;
            }
            if ($this->hasProFormaCredit() && !empty($amount)) {
                $lesson->addPayment($this, $amount, $enrolment);
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
                        $splitLesson->addPayment($this, $amount);
                        $this->makeInvoicePayment($splitLesson);
                    }
                }
            } else {
                if ($this->isExtraLessonProformaInvoice()) {
                    $lesson = Lesson::findOne($this->lineItem->lesson->id);
                }
                $amount = $lesson->proFormaLineItem->itemTotal - $lesson->getCreditAppliedAmount($lesson->enrolment->id);
                if ($amount > $this->proFormaCredit) {
                    $amount = $this->proFormaCredit;
                }
                if ($this->hasProFormaCredit() && !empty($amount)) {
                    $lesson->addPayment($this, $amount);
                    $this->makeInvoicePayment($lesson);
                }
            }
        }
    }

    public function addLessonCredit()
    {
	if ($this->lineItem) {
            if ($this->lineItem->isGroupLesson()) {
                $this->addGroupLessonCredit();
            } else {
                $this->addPrivateLessonCredit();
            }
        }
    }
    
    public function addLessonPayment($paymentId, $enrolmentId)
    {
        $lessonCredit  = new LessonPayment();
        $lessonCredit->lessonId = $this->id;
        $lessonCredit->paymentId = $paymentId;
        $lessonCredit->enrolmentId = $enrolmentId;
        $lessonCredit->save();
    }
}
