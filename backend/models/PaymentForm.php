<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Invoice;
use common\models\Lesson;
use common\models\Payment;
use common\models\Enrolment;
use common\models\User;
use common\models\LessonPayment;
use common\models\InvoicePayment;

/**
 * This is the model class for table "course".
 *
 * @property string $id
 * @property string $programId
 * @property string $teacherId
 * @property string $locationId
 * @property string $day
 * @property string $fromTime
 * @property string $startDate
 * @property string $endDate
 */
class PaymentForm extends Model
{
    const SCENARIO_BASIC = 'enrollment-basic';
    const SCENARIO_DETAILED = 'enrolment-detail';
    const SCENARIO_CUSTOMER = 'enrollment-customer';
    const SCENARIO_STUDENT = 'enrolment-student';
    const SCENARIO_DATE_DETAILED = 'enrolment-start-date';

    public $invoiceIds;
    public $date;
    public $payment_method_id;
    public $amount;
    public $amountNeeded;
    public $lessonId;
    public $userId;
    public $lessonIds;
    public $groupLessonIds;
    public $invoiceCreditIds;
    public $paymentCreditIds;
    public $invoicePayments;
    public $lessonPayments;
    public $groupLessonPayments;
    public $paymentCredits;
    public $invoiceCredits;
    public $amountToDistribute;
    public $canUseInvoiceCredits;
    public $canUsePaymentCredits;
    public $selectedCreditValue;
    public $paymentId;
    public $reference;
    public $receiptId;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['amount', 'validateAmount'],
            [['date', 'amountNeeded', 'invoiceIds', 'canUseInvoiceCredits', 'selectedCreditValue',
                'lessonIds', 'canUsePaymentCredits', 'invoiceCreditIds', 'amount', 'userId',
                'amountToDistribute', 'invoicePayments', 'lessonPayments','paymentId',
                'paymentCredits', 'invoiceCredits', 'reference', 'paymentCreditIds',
                'groupLessonIds', 'groupLessonPayments', 'receiptId', 'payment_method_id'], 'safe']
        ];
    }

    public function save()
    {
        $customer = User::findOne($this->userId);
        if ($this->invoiceIds) {
            $invoices = Invoice::find()
                ->where(['id' => $this->invoiceIds])
                ->orderBy(['id' => SORT_ASC])
                ->all();
        }
        if ($this->lessonIds) {
            $lessons = Lesson::find()
                ->where(['id' => $this->lessonIds])
                ->orderBy(['id' => SORT_ASC])
                ->all();
        }
        if ($this->groupLessonIds) {
            $groupLessons = Lesson::find()
                ->where(['id' => $this->groupLessonIds])
                ->orderBy(['id' => SORT_ASC])
                ->all();
        }
        if ($this->paymentCreditIds) {
            $creditPayments = Payment::find()
                ->where(['id' => $this->paymentCreditIds])
                ->orderBy(['id' => SORT_ASC])
                ->all();
        }
        if ($this->invoiceCreditIds) {
            $creditInvoices = Invoice::find()
                ->where(['id' => $this->invoiceCreditIds])
                ->orderBy(['id' => SORT_ASC])
                ->all();
        }
        $paymentCredits = $this->paymentCredits;
        $invoiceCredits = $this->invoiceCredits;
        $lessonPayments = $this->lessonPayments;
        $groupLessonPayments = $this->groupLessonPayments;
        $invoicePayments = $this->invoicePayments;
        if ($this->invoiceCreditIds) {
            if ($this->canUseInvoiceCredits) { 
                foreach ($creditInvoices as $j => $creditInvoice) {
                    if ($creditInvoice->hasCredit()) {
                        if ($invoiceCredits[$j] > abs($creditInvoice->balance)) {
                            $invoiceCredits[$j] = abs($creditInvoice->balance);
                        }
                        if ($this->invoiceIds) {
                            foreach ($invoices as $i => $invoice) {
                                if ($invoice->isOwing()) {
                                    if ($invoicePayments[$i] > $invoice->balance) {
                                        $invoicePayments[$i] = $invoice->balance;
                                    }
                                    if ($invoicePayments[$i] > 0.00) {
                                        $paymentModel = new Payment();
                                        $paymentModel->amount = $invoicePayments[$i];
                                        if ($invoiceCredits[$j] > 0.0) {
                                            if ($paymentModel->amount > $invoiceCredits[$j]) {
                                                $paymentModel->amount = $invoiceCredits[$j];
                                                $invoicePayments[$i] -= $invoiceCredits[$j];
                                                $invoiceCredits[$j] -= $paymentModel->amount;
                                            }
                                            $invoice->addPayment($creditInvoice, $paymentModel);
                                        } else {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if ($this->lessonIds) {
                            foreach ($lessons as $i => $lesson) {
                                if ($lesson->isOwing($lesson->enrolment->id)) {
                                    if ($lessonPayments[$i] > $lesson->getOwingAmount($lesson->enrolment->id)) {
                                        $lessonPayments[$i] = $lesson->getOwingAmount($lesson->enrolment->id);
                                    }
                                    if ($lessonPayments[$i] > 0.00) {
                                        $paymentModel = new Payment();
                                        $paymentModel->amount = $lessonPayments[$i];
                                        if ($invoiceCredits[$j] > 0.00) {
                                            if ($paymentModel->amount > $invoiceCredits[$j]) {
                                                $paymentModel->amount = $invoiceCredits[$j];
                                                $lessonPayments[$i] -= $invoiceCredits[$j];
                                                $invoiceCredits[$j] -= $paymentModel->amount;
                                            }
                                            $lesson->addPayment($creditInvoice, $paymentModel);
                                        } else {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if ($this->groupLessonIds) {
                            foreach ($groupLessons as $i => $lesson) {
                                $enrolment = Enrolment::find()
                                    ->notDeleted()
                                    ->isConfirmed()
                                    ->andWhere(['courseId' => $lesson->courseId])
                                    ->customer($this->userId)
                                    ->one();
                                if ($lesson->isOwing($enrolment->id)) {
                                    if ($groupLessonPayments[$i] > $lesson->getOwingAmount($enrolment->id)) {
                                        $groupLessonPayments[$i] = $lesson->getOwingAmount($enrolment->id);
                                    }
                                    if ($groupLessonPayments[$i] > 0.00) {
                                        $paymentModel = new Payment();
                                        $paymentModel->amount = $groupLessonPayments[$i];
                                        if ($invoiceCredits[$j] > 0.00) {
                                            if ($paymentModel->amount > $invoiceCredits[$j]) {
                                                $paymentModel->amount = $invoiceCredits[$j];
                                                $groupLessonPayments[$i] -= $invoiceCredits[$j];
                                                $invoiceCredits[$j] -= $paymentModel->amount;
                                            }
                                            $lesson->addPayment($creditInvoice, $paymentModel, $enrolment);
                                        } else {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($this->paymentCreditIds) {
            if ($this->canUsePaymentCredits) {
                foreach ($creditPayments as $j => $creditPayment) {
                    if ($creditPayment->hasCredit()) {
                        if ($paymentCredits[$j] > abs($creditPayment->creditAmount)) {
                            $paymentCredits[$j] = abs($creditPayment->creditAmount);
                        }
                        if ($this->invoiceIds) {
                            foreach ($invoices as $i => $invoice) {
                                if ($invoice->isOwing()) {
                                    if ($invoicePayments[$i] > $invoice->balance) {
                                        $invoicePayments[$i] = $invoice->balance;
                                    }
                                    if ($paymentCredits[$j] > 0.00) {
                                        if ($invoicePayments[$i] > $paymentCredits[$j]) {
                                            $amountToPay = $paymentCredits[$j];
                                            $invoicePayments[$i] -= $amountToPay;
                                            $paymentCredits[$j] -= $amountToPay;
                                        } else {
                                            $amountToPay = $invoicePayments[$i];
                                        }
                                        $invoicePaymentModel = new InvoicePayment();
                                        $invoicePaymentModel->invoice_id = $invoice->id;
                                        $invoicePaymentModel->payment_id = $creditPayment->id;
                                        $invoicePaymentModel->receiptId  = $this->receiptId;
                                        $invoicePaymentModel->amount     = $amountToPay;
                                        $invoicePaymentModel->save();
                                        $invoice->save();
                                    } else {
                                        break;
                                    }
                                }
                            }
                        }
                        if ($this->lessonIds) {
                            foreach ($lessons as $i => $lesson) {
                                if ($lesson->isOwing($lesson->enrolment->id)) {
                                    if ($lessonPayments[$i] > $lesson->getOwingAmount($lesson->enrolment->id)) {
                                        $lessonPayments[$i] = $lesson->getOwingAmount($lesson->enrolment->id);
                                    }
                                    if ($paymentCredits[$j] > 0.00) {
                                        if ($lessonPayments[$i] > $paymentCredits[$j]) {
                                            $amountToPay = $paymentCredits[$j];
                                        } else {
                                            $amountToPay = $lessonPayments[$i];
                                        }
                                        $lessonPayments[$i] -= $amountToPay;
                                        $paymentCredits[$j] -= $amountToPay;
                                        $lessonPaymentModel = new LessonPayment();
                                        $lessonPaymentModel->lessonId = $lesson->id;
                                        $lessonPaymentModel->paymentId = $creditPayment->id;
                                        $lessonPaymentModel->receiptId  = $this->receiptId;
                                        $lessonPaymentModel->enrolmentId = $lesson->enrolment->id;
                                        $lessonPaymentModel->amount     = $amountToPay;
                                        $lessonPaymentModel->save();
                                    } else {
                                        break;
                                    }
                                }
                            }
                        }
                        if ($this->groupLessonIds) {
                            foreach ($groupLessons as $i => $lesson) {
                                $enrolment = Enrolment::find()
                                    ->notDeleted()
                                    ->isConfirmed()
                                    ->andWhere(['courseId' => $lesson->courseId])
                                    ->customer($this->userId)
                                    ->one();
                                if ($lesson->isOwing($enrolment->id)) {
                                    if ($groupLessonPayments[$i] > $lesson->getOwingAmount($enrolment->id)) {
                                        $groupLessonPayments[$i] = $lesson->getOwingAmount($enrolment->id);
                                    }
                                    if ($paymentCredits[$j] > 0.00) {
                                        if ($groupLessonPayments[$i] > $paymentCredits[$j]) {
                                            $amountToPay = $paymentCredits[$j];
                                        } else {
                                            $amountToPay = $groupLessonPayments[$i];
                                        }
                                        $groupLessonPayments[$i] -= $amountToPay;
                                        $paymentCredits[$j] -= $amountToPay;
                                        $lessonPaymentModel = new LessonPayment();
                                        $lessonPaymentModel->lessonId = $lesson->id;
                                        $lessonPaymentModel->paymentId = $creditPayment->id;
                                        $lessonPaymentModel->receiptId  = $this->receiptId;
                                        $lessonPaymentModel->enrolmentId = $enrolment->id;
                                        $lessonPaymentModel->amount     = $amountToPay;
                                        $lessonPaymentModel->save();
                                    } else {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        $amount = $this->amount;
        if ($this->invoiceIds) {
            foreach ($invoices as $i => $invoice) {
                if ($invoice->isOwing()) {
                    if ($invoicePayments[$i] > $invoice->balance) {
                        $invoicePayments[$i] = $invoice->balance;
                    }
                    if ($invoicePayments[$i] > 0.00) {
                        if ($amount > 0.00) {
                            if ($amount > $invoicePayments[$i]) {
                                $amountToPay = $invoice->balance;
                            } else {
                                $amountToPay = $amount;
                            }
                            $invoicePaymentModel = new InvoicePayment();
                            $invoicePaymentModel->invoice_id = $invoice->id;
                            $invoicePaymentModel->payment_id = $this->paymentId;
                            $invoicePaymentModel->receiptId  = $this->receiptId;
                            $invoicePaymentModel->amount     = $amountToPay;
                            $invoicePaymentModel->save();
                            $invoice->save();
                            $amount -= $amountToPay;
                        } else {
                            break;
                        }
                    }
                }
            }
        }
        if ($this->lessonIds) {
            foreach ($lessons as $i => $lesson) {
                if ($lesson->isOwing($lesson->enrolment->id)) {
                    if ($lessonPayments[$i] > $lesson->getOwingAmount($lesson->enrolment->id)) {
                        $lessonPayments[$i] = $lesson->getOwingAmount($lesson->enrolment->id);
                    }
                    if ($lessonPayments[$i] > 0.00) {
                        if ($amount > 0.00) {
                            $lessonPayment = new LessonPayment();
                            $lessonPayment->lessonId    = $lesson->id;
                            $lessonPayment->paymentId   = $this->paymentId;
                            $lessonPayment->amount      = $lessonPayments[$i];
                            $lessonPayment->enrolmentId = $lesson->enrolment->id;
                            $lessonPayment->receiptId  = $this->receiptId;
                            $lessonPayment->save();
                        } else {
                            break;
                        }
                    }
                }
            }
        }
        if ($this->groupLessonIds) {
            foreach ($groupLessons as $i => $lesson) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $lesson->courseId])
                    ->customer($this->userId)
                    ->one();
                if ($lesson->isOwing($enrolment->id)) {
                    if ($groupLessonPayments[$i] > $lesson->getOwingAmount($enrolment->id)) {
                        $groupLessonPayments[$i] = $lesson->getOwingAmount($enrolment->id);
                    }
                    if ($groupLessonPayments[$i] > 0.00) {
                        if ($amount > 0.00) {
                            $lessonPayment = new LessonPayment();
                            $lessonPayment->lessonId    = $lesson->id;
                            $lessonPayment->paymentId   = $this->paymentId;
                            $lessonPayment->receiptId  = $this->receiptId;
                            $lessonPayment->amount      = $groupLessonPayments[$i];
                            $lessonPayment->enrolmentId = $enrolment->id;
                            $lessonPayment->save();
                        } else {
                            break;
                        }
                    }
                }
            }
        }
        return true;
    }

    public function getCustomerCreditInvoices($customerId)
    {
        return Invoice::find()
            ->notDeleted()
            ->notCanceled()
            ->invoiceCredit($customerId)
            ->all();
    }

    public function validateAmount($attributes)
    {
        if ($this->amount < 0.01 && $this->selectedCreditValue < 0.01) {
            if ($this->amountNeeded > 0.01) {
                $this->addError($attributes, "Amount can't be empty");
            }
        }

        if (is_numeric($this->amountToDistribute)) {
            if (round($this->amountToDistribute, 2) > (round($this->selectedCreditValue, 2) + round($this->amount, 2))) {
                $this->addError($attributes, "Amount mismatched with distributions");
            }
        } else {
            $this->addError($attributes, "Amount must be number");
        }
    }
}
