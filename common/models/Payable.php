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
        $paymentModel->sourceId = $payment->sourceId;
        $paymentModel->amount = $payment->amount;
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
        if ($paymentModel->save()) {
            $creditPaymentId = $paymentModel->id;
            if ($this->tableName() === 'lesson') {
                if (!$enrolment) {
                    $enrolment = $this->enrolment;
                }
                $lessonPayment = $this->addLessonPayment($creditPaymentId, $enrolment->id);
                if (!$lessonPayment) {
                    $transaction->rollBack();
                }
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
            if ($paymentModel->save()) {
                $debitPaymentId = $paymentModel->id;
                if ($from->tableName() === 'lesson') {
                    if (!$enrolment) {
                        $enrolment = $from->enrolment;
                    }
                    $lessonPayment = $from->addLessonPayment($debitPaymentId, $enrolment->id);
                    if (!$lessonPayment) {
                        $transaction->rollBack();
                    }
                }
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
            if ($lesson->isExploded) {
                $parentLesson = $lesson->parent()->one();
                $splitLessons = Lesson::find()
                        ->descendantsOf($parentLesson->id)
                        ->orderBy(['id' => SORT_DESC])
                        ->all();
                foreach ($splitLessons as $splitLesson) {
                    $amount = $splitLesson->getSplitedAmount();
                    if ($amount > $this->proFormaCredit) {
                        $amount = $this->proFormaCredit;
                    }
                    $payment->amount = $amount;
                    if ($this->hasProFormaCredit() && !empty($amount)) {
                        if ($splitLesson->hasMerged()) {
                            $splitLesson = $splitLesson->extendedLesson;
                        }
                        $splitLesson->addPayment($this, $payment);
                        $this->makeInvoicePayment($splitLesson);
                    }
                }
            } else {
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
