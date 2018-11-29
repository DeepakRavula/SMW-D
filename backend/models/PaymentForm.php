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
    public $prId;
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
    public $notes;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['amount', 'validateAmount'],
            ['amount', 'match', 'pattern' => '^[0-9]\d*(\.\d+)?$^'],
            [['date', 'amountNeeded', 'invoiceIds', 'canUseInvoiceCredits', 'selectedCreditValue',
                'lessonIds', 'canUsePaymentCredits', 'invoiceCreditIds', 'amount', 'userId',
                'amountToDistribute', 'invoicePayments', 'lessonPayments','paymentId',
                'paymentCredits', 'invoiceCredits', 'reference', 'paymentCreditIds', 'prId',
                'groupLessonIds', 'groupLessonPayments', 'receiptId', 'payment_method_id', 'notes'], 'safe']
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
                ->orderBy(['date' => SORT_ASC])
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
                        if (round($invoiceCredits[$j], 2) > round(abs($creditInvoice->balance), 2)) {
                            $invoiceCredits[$j] = round(abs($creditInvoice->balance), 2);
                        }
                        if ($this->invoiceIds) {
                            foreach ($invoices as $i => $invoice) {
                                if ($invoice->isOwing()) {
                                    if (round($invoicePayments[$i], 2) > round($invoice->balance, 2)) {
                                        $invoicePayments[$i] = round($invoice->balance, 2);
                                    }
                                    if (round($invoicePayments[$i], 2) > 0.00) {
                                        $paymentModel = new Payment();
                                        $paymentModel->amount = round($invoicePayments[$i], 2);
                                        if (round($invoiceCredits[$j], 2) > 0.0) {
                                            if (round($paymentModel->amount, 2) > round($invoiceCredits[$j], 2)) {
                                                $paymentModel->amount = round($invoiceCredits[$j], 2);                                
                                            }
                                            $invoicePayments[$i] -= round($paymentModel->amount, 2);
                                            $invoiceCredits[$j] -= round($paymentModel->amount, 2);
                                            $invoice->addPayment($creditInvoice, $paymentModel);
                                        } else {
                                            break;
                                        }
                                    }
                                    $invoice->save();
                                }
                            }
                        }
                        if ($this->lessonIds) {
                            foreach ($lessons as $i => $lesson) {
                                if ($lesson->isOwing($lesson->enrolment->id)) {
                                    if (round($lessonPayments[$i], 2) > round($lesson->getOwingAmount($lesson->enrolment->id), 2)) {
                                        $lessonPayments[$i] = round($lesson->getOwingAmount($lesson->enrolment->id), 2);
                                    }
                                    if (round($lessonPayments[$i], 2) > 0.00) {
                                        $paymentModel = new Payment();
                                        $paymentModel->amount = round($lessonPayments[$i], 2);
                                        if (round($invoiceCredits[$j], 2) > 0.00) {
                                            if (round($paymentModel->amount, 2) > round($invoiceCredits[$j], 2)) {
                                                $paymentModel->amount = round($invoiceCredits[$j], 2);
                                            }
                                            $lessonPayments[$i] -= round($paymentModel->amount, 2);
                                            $invoiceCredits[$j] -= round($paymentModel->amount, 2);
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
                                    if (round($groupLessonPayments[$i], 2) > round($lesson->getOwingAmount($enrolment->id), 2)) {
                                        $groupLessonPayments[$i] = round($lesson->getOwingAmount($enrolment->id), 2);
                                    }
                                    if (round($groupLessonPayments[$i], 2) > 0.00) {
                                        $paymentModel = new Payment();
                                        $paymentModel->amount = round($groupLessonPayments[$i], 2);
                                        if (round($invoiceCredits[$j], 2) > 0.00) {
                                            if (round($paymentModel->amount, 2) > round($invoiceCredits[$j], 2)) {
                                                $paymentModel->amount = round($invoiceCredits[$j], 2);                               
                                            }
                                            $groupLessonPayments[$i] -=round($paymentModel->amount, 2);
                                            $invoiceCredits[$j] -= round($paymentModel->amount, 2);
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
                        if (round($paymentCredits[$j], 2) > round(abs($creditPayment->creditAmount), 2)) {
                            $paymentCredits[$j] = round(abs($creditPayment->creditAmount), 2);
                        }
                        if ($this->invoiceIds) {
                            foreach ($invoices as $i => $invoice) {
                                if ($invoice->isOwing()) {
                                    if (round($invoicePayments[$i], 2) > round($invoice->balance, 2)) {
                                        $invoicePayments[$i] = round($invoice->balance, 2);
                                    }
                                    if (round($paymentCredits[$j], 2) > 0.00) {
                                        if (round($invoicePayments[$i], 2) > round($paymentCredits[$j], 2)) {
                                            $amountToPay =round($paymentCredits[$j], 2);                                
                                        } else {
                                            $amountToPay = round($invoicePayments[$i], 2);
                                        }
                                        $invoicePayments[$i] -= round($amountToPay, 2);
                                        $paymentCredits[$j] -= round($amountToPay, 2);
                                        $invoicePaymentModel = new InvoicePayment();
                                        $invoicePaymentModel->invoice_id = $invoice->id;
                                        $invoicePaymentModel->payment_id = $creditPayment->id;
                                        $invoicePaymentModel->receiptId  = $this->receiptId;
                                        $invoicePaymentModel->amount     = round($amountToPay, 2);
                                        $invoicePaymentModel->save();
                                        $invoice->save();
                                    } else {
                                        break;
                                    }
                                }
                                $invoice->save();
                            }
                        }
                        if ($this->lessonIds) {
                            foreach ($lessons as $i => $lesson) {
                                if ($lesson->isOwing($lesson->enrolment->id)) {
                                    if (round($lessonPayments[$i], 2) > round($lesson->getOwingAmount($lesson->enrolment->id), 2)) {
                                        $lessonPayments[$i] = round($lesson->getOwingAmount($lesson->enrolment->id), 2);
                                    }
                                    if (round($paymentCredits[$j], 2) > 0.00) {
                                        if (round($lessonPayments[$i], 2) > round($paymentCredits[$j], 2)) {
                                            $amountToPay = round($paymentCredits[$j], 2);
                                        } else {
                                            $amountToPay = round($lessonPayments[$i], 2);
                                        }
                                        $lessonPayments[$i] -= round($amountToPay, 2);
                                        $paymentCredits[$j] -= round($amountToPay, 2);
                                        $lessonPaymentModel = new LessonPayment();
                                        $lessonPaymentModel->lessonId = $lesson->id;
                                        $lessonPaymentModel->paymentId = $creditPayment->id;
                                        $lessonPaymentModel->receiptId  = $this->receiptId;
                                        $lessonPaymentModel->enrolmentId = $lesson->enrolment->id;
                                        $lessonPaymentModel->amount     = round($amountToPay, 2);
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
                                    if (round($groupLessonPayments[$i], 2) > round($lesson->getOwingAmount($enrolment->id), 2)) {
                                        $groupLessonPayments[$i] = round($lesson->getOwingAmount($enrolment->id), 2);
                                    }
                                    if (round($paymentCredits[$j], 2) > 0.00) {
                                        if (round($groupLessonPayments[$i], 2) > round($paymentCredits[$j], 2)) {
                                            $amountToPay = round($paymentCredits[$j], 2);
                                        } else {
                                            $amountToPay = round($groupLessonPayments[$i], 2);
                                        }
                                        $groupLessonPayments[$i] -= round($amountToPay, 2);
                                        $paymentCredits[$j] -=  round($amountToPay, 2);
                                        $lessonPaymentModel = new LessonPayment();
                                        $lessonPaymentModel->lessonId = $lesson->id;
                                        $lessonPaymentModel->paymentId = $creditPayment->id;
                                        $lessonPaymentModel->receiptId  = $this->receiptId;
                                        $lessonPaymentModel->enrolmentId = $enrolment->id;
                                        $lessonPaymentModel->amount     =  round($amountToPay, 2);
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
        
        $amount =  $this->amount;
        if ($this->invoiceIds) {
            foreach ($invoices as $i => $invoice) {
                if ($invoice->isOwing()) {
                    if (round($invoicePayments[$i], 2) > round($invoice->balance, 2)) {
                        $invoicePayments[$i] =  round($invoice->balance, 2);
                    }
                    if (round($invoicePayments[$i], 2) > 0.00) {
                        if (round($amount, 2) > 0.00) {
                            if (round($amount, 2) > round($invoicePayments[$i], 2)) {
                                $amountToPay = round($invoicePayments[$i], 2);
                            } else {
                                $amountToPay = round($amount, 2);
                            }
                            $invoicePaymentModel = new InvoicePayment();
                            $invoicePaymentModel->invoice_id = $invoice->id;
                            $invoicePaymentModel->payment_id = $this->paymentId;
                            $invoicePaymentModel->receiptId  = $this->receiptId;
                            $invoicePaymentModel->amount     = round($amountToPay, 2);
                            $invoicePaymentModel->save();
                            $invoice->save();
                            $amount -= round($amountToPay, 2);
                        } else {
                            break;
                        }
                    }
                    $invoice->save();
                }
            }
        }
        if ($this->lessonIds) {
            foreach ($lessons as $i => $lesson) {
                if ($lesson->isOwing($lesson->enrolment->id)) {
                    if ( round($lessonPayments[$i], 2) > round($lesson->getOwingAmount($lesson->enrolment->id), 2)) {
                        $lessonPayments[$i] = round($lesson->getOwingAmount($lesson->enrolment->id), 2);
                    }
                    if ( round($lessonPayments[$i], 2) > 0.00) {
                        if ( round($amount, 2) > 0.00) {
                            $lessonPayment = new LessonPayment();
                            $lessonPayment->lessonId    = $lesson->id;
                            $lessonPayment->paymentId   = $this->paymentId;
                            $lessonPayment->amount      = $lessonPayments[$i];
                            $lessonPayment->enrolmentId = $lesson->enrolment->id;
                            $lessonPayment->receiptId  = $this->receiptId;
                            $lessonPayment->save();
                            $amount -= round($lessonPayments[$i], 2);
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
                    if ( round($groupLessonPayments[$i], 2) > round($lesson->getOwingAmount($enrolment->id), 2)) {
                        $groupLessonPayments[$i] = round($lesson->getOwingAmount($enrolment->id), 2);
                    }
                    if ( round($groupLessonPayments[$i], 2) > 0.00) {
                        if ( round($amount, 2) > 0.00) {
                            $lessonPayment = new LessonPayment();
                            $lessonPayment->lessonId    = $lesson->id;
                            $lessonPayment->paymentId   = $this->paymentId;
                            $lessonPayment->receiptId  = $this->receiptId;
                            $lessonPayment->amount      = round($groupLessonPayments[$i], 2);
                            $lessonPayment->enrolmentId = $enrolment->id;
                            $lessonPayment->save();
                            $amount -= round($groupLessonPayments[$i], 2);
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
            if (round($this->amountToDistribute, 2) > round(($this->selectedCreditValue + $this->amount), 2)) {
                $this->addError($attributes, "Amount mismatched with distributions");
            }
        } else {
            $this->addError($attributes, "Amount must be number");
        }
    }
}
