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
                $lessonDate = new \DateTime($lesson->date);
                $enrolmentDate = new \DateTime($lesson->enrolment->createdAt);
                if ($enrolmentDate < $lessonDate) {
                    $invoice = $lesson->createPrivateLessonInvoice();
                }
            } elseif (!$lesson->invoice->isPaid()) {
                if ($lesson->hasLessonCredit($lesson->enrolment->id)) {
                    $netPrice = $lesson->getLessonCreditAmount($lesson->enrolment->id);
                    if ($lesson->isExploded) {
                        $netPrice = $lesson->getSplitedAmount() - $lesson->
                                getCreditUsedAmount($lesson->enrolment->id);
                    }
                    $payment = new Payment();
                    $payment->amount = $netPrice;
                    $lesson->invoice->addPayment($lesson, $payment, $lesson->enrolment);
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
                    $payment = new Payment();
                    $payment->amount = $netPrice;
                    $enrolment->getInvoice($lesson->id)->addPayment(
                        $lesson,
                        $payment,
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
        return $creditUsageModel->save();
    }
    
    public function addPayment($from, $payment, $enrolment = null)
    {
        if (round($payment->amount, 2) < round(0.00, 2)) {
            return true;
        }
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        $paymentModel = new Payment();
        $paymentModel->date = $payment->date;
        $paymentModel->sourceId = $payment->sourceId;
        $paymentModel->amount = round($payment->amount, 2);
        $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
        if ($this->tableName() === 'invoice') {
            $paymentModel->invoiceId = $this->id;
            $user = $this->user;
        } else if ($this->tableName() === 'lesson') {
            $user = $this->customer;
            $paymentModel->lessonId = $this->id;
            if (!$enrolment) {
                $enrolment = $this->enrolment;
            }
            $paymentModel->enrolmentId = $enrolment->id;
        } else if ($this->tableName() === 'user') {
            $paymentModel->customerId = $this->id;
            $user = $this;
        }
        if ($from->tableName() === 'lesson') {
            $paymentModel->reference = $from->getLessonNumber();
        } else if ($from->tableName() === 'invoice') {
            $paymentModel->reference = $from->getInvoiceNumber();
        }
        $paymentModel->user_id = $user->id;
        if ($paymentModel->save()) {
            $creditPaymentId = $paymentModel->id;
            $paymentModel->id = null;
            $paymentModel->isNewRecord = true;
            $paymentModel->setScenario(Payment::SCENARIO_CREDIT_USED);
            $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
            $paymentModel->invoiceId = null;
            $paymentModel->sourceId = null;
            $paymentModel->lessonId = null;
            $paymentModel->customerId = null;
            $paymentModel->reference = null;
            if ($from->tableName() === 'invoice') {
                $paymentModel->invoiceId = $from->id;
            } else if ($from->tableName() === 'user') {
                $paymentModel->customerId = $from->id;
            } else if ($from->tableName() === 'lesson') {
                if (!$enrolment) {
                    $enrolment = $from->enrolment;
                }
                $paymentModel->lessonId = $from->id;
                $paymentModel->enrolmentId = $enrolment->id;
            }
            
            if ($this->tableName() === 'invoice') {
                $paymentModel->reference = $this->getInvoiceNumber();
            } else if ($this->tableName() === 'lesson') {
                $paymentModel->reference = $this->getLessonNumber();
            }
            $paymentModel->amount = -abs($paymentModel->amount);
            if ($paymentModel->save()) {
                $debitPaymentId = $paymentModel->id;
                $creditMapping = $this->createCreditUsage($creditPaymentId, $debitPaymentId);
                if ($creditMapping) {
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } else {
                $transaction->rollBack();
            }
        } else {
            $transaction->rollBack();
        }
        return true;
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
                $payment = new Payment();
                $payment->amount = $amount;
                $lesson->addPayment($this, $payment, $enrolment);
                $this->makeGroupInvoicePayment($lesson, $enrolment);
            }
        }
    }
    
    public function addPrivateLessonCredit()
    {
        $payment = new Payment();
        foreach ($this->lineItems as $lineItem) {
            $lesson = Lesson::find()
                        ->descendantsOf($lineItem->proFormaLesson->id)
                        ->orderBy(['id' => SORT_DESC])
                        ->one();
            if (!$lesson) {
                $lesson = Lesson::findOne($lineItem->proFormaLesson->id);
            }
            if ($this->isExtraLessonProformaInvoice()) {
                $lesson = Lesson::findOne($this->lineItem->lesson->id);
            }
            $amount = $lesson->proFormaLineItem->itemTotal;
            if ($amount > $this->proFormaCredit) {
                $amount = $this->proFormaCredit;
            }
            if ($this->hasProFormaCredit() && !empty($amount)) {
                $payment->amount = $amount;
                $lesson->addPayment($this, $payment);
                $this->makeInvoicePayment($lesson);
            }
            if ($lesson->hasMerged()) {
                $extendedLesson = $lesson->extendedLesson;
                $extendedLesson->addPayment($lesson, $payment);
                $this->makeInvoicePayment($extendedLesson);
            }
        }
        return true;
    }

    public function distributeCreditsToLesson()
    {
	    if ($this->lineItem && $this->canDistributeCreditsToLesson()) {
            if ($this->lineItem->isGroupLesson()) {
                $status = $this->addGroupLessonCredit();
            } else {
                $status = $this->addPrivateLessonCredit();
            }
        }
        $this->save();
        return true;
    }
    
    public function addLessonPayment($paymentId, $enrolmentId)
    {
        $lessonCredit  = new LessonPayment();
        $lessonCredit->lessonId = $this->id;
        $lessonCredit->paymentId = $paymentId;
        $lessonCredit->enrolmentId = $enrolmentId;
        return $lessonCredit->save();
    }
}
