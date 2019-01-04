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
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $customer = User::findOne($this->userId);
        $paymentCredits = $this->paymentCredits;
        $invoiceCredits = $this->invoiceCredits;
        $lessonPayments = $this->lessonPayments;
        $groupLessonPayments = $this->groupLessonPayments;
        $invoicePayments = $this->invoicePayments;
        if ($invoiceCredits) {
            if ($this->canUseInvoiceCredits) { 
                foreach ($invoiceCredits as $invoiceCredit) {
                    $creditInvoice = Invoice::findOne($invoiceCredit['id']);
                    $creditInvoiceAmount = $invoiceCredit['value'];
                    if ($creditInvoice->hasCredit()) {
                        if (round($creditInvoiceAmount, 2) > round(abs($creditInvoice->balance), 2)) {
                            $creditInvoiceAmount = round(abs($creditInvoice->balance), 2);
                        }
                        if ($invoicePayments) {
                            foreach ($invoicePayments as $i => $invoicePayment) {
                                $invoice = Invoice::findOne($invoicePayment['id']);
                                $invoicePaymentAmount = $invoicePayment['value'];
                                if ($invoice->isOwing()) {
                                    if (round($invoicePaymentAmount, 2) > round($invoice->balance, 2)) {
                                        $invoicePaymentAmount = round($invoice->balance, 2);
                                    }
                                    if (round($invoicePaymentAmount, 2) > 0.00) {
                                        $paymentModel = new Payment();
                                        $paymentModel->amount = round($invoicePaymentAmount, 2);
                                        if (round($creditInvoiceAmount, 2) > 0.0) {
                                            if (round($paymentModel->amount, 2) > round($creditInvoiceAmount, 2)) {
                                                $paymentModel->amount = round($creditInvoiceAmount, 2);                                
                                            }
                                            $invoicePaymentAmount -= round($paymentModel->amount, 2);
                                            $creditInvoiceAmount -= round($paymentModel->amount, 2);
                                            $invoice->addPayment($creditInvoice, $paymentModel);
                                        } else {
                                            break;
                                        }
                                    }
                                    $invoice->save();
                                }
                            }
                        }
                        if ($lessonPayments) {
                            foreach ($lessonPayments as $lessonPayment) {
                                $lesson = Lesson::findOne($lessonPayment['id']);
                                $lessonPaymentAmount = $lessonPayment['value'];
                                if ($lesson->isOwing($lesson->enrolment->id)) {
                                    if (round($lessonPaymentAmount, 2) > round($lesson->getOwingAmount($lesson->enrolment->id), 2)) {
                                        $lessonPaymentAmount = round($lesson->getOwingAmount($lesson->enrolment->id), 2);
                                    }
                                    if (round($lessonPaymentAmount, 2) > 0.00) {
                                        $paymentModel = new Payment();
                                        $paymentModel->amount = round($lessonPaymentAmount, 2);
                                        if (round($creditInvoiceAmount, 2) > 0.00) {
                                            if (round($paymentModel->amount, 2) > round($creditInvoiceAmount, 2)) {
                                                $paymentModel->amount = round($creditInvoiceAmount, 2);
                                            }
                                            $lessonPaymentAmount -= round($paymentModel->amount, 2);
                                            $creditInvoiceAmount -= round($paymentModel->amount, 2);
                                            $lesson->addPayment($creditInvoice, $paymentModel);
                                        } else {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if ($groupLessonPayments) {
                            foreach ($groupLessonPayments as $groupLessonPayment) {
                                $enrolment = Enrolment::find()
                                    ->notDeleted()
                                    ->isConfirmed()
                                    ->andWhere(['courseId' => $groupLesson->courseId])
                                    ->customer($this->userId)
                                    ->one();
                                $groupLesson = Lesson::findOne($groupLessonPayment['id']);
                                $groupLessonPaymentAmount = $groupLessonPayment['value'];
                                if ($groupLesson->isOwing($enrolment->id)) {
                                    if (round($groupLessonPaymentAmount, 2) > round($groupLesson->getOwingAmount($enrolment->id), 2)) {
                                        $groupLessonPaymentAmount = round($groupLesson->getOwingAmount($enrolment->id), 2);
                                    }
                                    if (round($groupLessonPaymentAmount, 2) > 0.00) {
                                        $paymentModel = new Payment();
                                        $paymentModel->amount = round($groupLessonPaymentAmount, 2);
                                        if (round($creditInvoiceAmount, 2) > 0.00) {
                                            if (round($paymentModel->amount, 2) > round($creditInvoiceAmount, 2)) {
                                                $paymentModel->amount = round($creditInvoiceAmount, 2);                               
                                            }
                                            $groupLessonPaymentAmount -=round($paymentModel->amount, 2);
                                            $creditInvoiceAmount -= round($paymentModel->amount, 2);
                                            $groupLesson->addPayment($creditInvoice, $paymentModel, $enrolment);
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
        if ($paymentCredits) {
            if ($this->canUsePaymentCredits) {
                foreach ($paymentCredits as $paymentCredit) {
                    $creditPayment = Payment::findOne($paymentCredit['id']);
                    $creditPaymentAmount = $paymentCredit['value'];
                    if ($creditPayment->hasCredit()) {
                        if (round($creditPaymentAmount, 2) > round(abs($creditPayment->creditAmount), 2)) {
                            $creditPaymentAmount = round(abs($creditPayment->creditAmount), 2);
                        }
                        if ($invoicePayment) {
                            foreach ($invoicePayments as $invoicePayment) {
                                $invoice = Invoice::findOne($invoicePayment['id']);
                                $invoicePaymentAmount = $invoicePayment['value'];
                                if ($invoice->isOwing()) {
                                    if (round($invoicePaymentAmount, 2) > round($invoice->balance, 2)) {
                                        $invoicePaymentAmount = round($invoice->balance, 2);
                                    }
                                    if (round($creditPaymentAmount, 2) > 0.00) {
                                        if (round($invoicePaymentAmount, 2) > round($creditPaymentAmount, 2)) {
                                            $amountToPay =round($creditPaymentAmount, 2);                                
                                        } else {
                                            $amountToPay = round($invoicePaymentAmount, 2);
                                        }
                                        $invoicePaymentAmount -= round($amountToPay, 2);
                                        $creditPaymentAmount -= round($amountToPay, 2);
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
                        if ($lessonPayments) {
                            foreach ($lessonPayments as $lessonPayment) {
                                $lesson = Lesson::findOne($lessonPayment['id']);
                                $lessonPaymentAmount = $lessonPayment['value'];
                                if ($lesson->isOwing($lesson->enrolment->id)) {
                                    if (round($lessonPaymentAmount, 2) > round($lesson->getOwingAmount($lesson->enrolment->id), 2)) {
                                        $lessonPaymentAmount = round($lesson->getOwingAmount($lesson->enrolment->id), 2);
                                    }
                                    if (round($creditPaymentAmount, 2) > 0.00) {
                                        if (round($lessonPaymentAmount, 2) > round($creditPaymentAmount, 2)) {
                                            $amountToPay = round($creditPaymentAmount, 2);
                                        } else {
                                            $amountToPay = round($lessonPaymentAmount, 2);
                                        }
                                        $lessonPaymentAmount -= round($amountToPay, 2);
                                        $creditPaymentAmount -= round($amountToPay, 2);
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
                        if ($groupLessonPayments) {
                            foreach ($groupLessonPayments as $groupLessonPayment) {
                                $enrolment = Enrolment::find()
                                    ->notDeleted()
                                    ->isConfirmed()
                                    ->andWhere(['courseId' => $lesson->courseId])
                                    ->customer($this->userId)
                                    ->one();
                                $groupLesson = Lesson::findOne($groupLessonPayment['id']);
                                $groupLessonPaymentAmount = $groupLessonPayment['value'];
                                if ($groupLesson->isOwing($enrolment->id)) {
                                    if (round($grouplessonPaymentAmount, 2) > round($groupLesson->getOwingAmount($enrolment->id), 2)) {
                                        $grouplessonPaymentAmount = round($groupLesson->getOwingAmount($enrolment->id), 2);
                                    }
                                    if (round($creditPaymentAmount, 2) > 0.00) {
                                        if (round($grouplessonPaymentAmount, 2) > round($creditPaymentAmount, 2)) {
                                            $amountToPay = round($creditPaymentAmount, 2);
                                        } else {
                                            $amountToPay = round($grouplessonPaymentAmount, 2);
                                        }
                                        $grouplessonPaymentAmount -= round($amountToPay, 2);
                                        $creditPaymentAmount -=  round($amountToPay, 2);
                                        $lessonPaymentModel = new LessonPayment();
                                        $lessonPaymentModel->lessonId = $groupLesson->id;
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
        if ($invoicePayments) {
            foreach ($invoicePayments as $invoicePayment) {
                $invoice = Invoice::findOne($invoicePayment['id']);
                $invoicePaymentAmount = $invoicePayment['value'];
                if ($invoice->isOwing()) {
                    if (round($invoicePaymentAmount, 2) > round($invoice->balance, 2)) {
                        $invoicePaymentAmount =  round($invoice->balance, 2);
                    }
                    if (round($invoicePaymentAmount, 2) > 0.00) {
                        if (round($amount, 2) > 0.00) {
                            if (round($amount, 2) > round($invoicePaymentAmount, 2)) {
                                $amountToPay = round($invoicePaymentAmount, 2);
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
        if ($lessonPayments) {
            foreach ($lessonPayments as $lessonPayment) {
                $lesson = Lesson::findOne($lessonPayment['id']);
                $lessonPaymentAmount = $lessonPayment['value'];
                if ($lesson->isOwing($lesson->enrolment->id)) {
                    if ( round($lessonPaymentAmount, 2) > round($lesson->getOwingAmount($lesson->enrolment->id), 2)) {
                        $lessonPaymentAmount = round($lesson->getOwingAmount($lesson->enrolment->id), 2);
                    }
                    if ( round($lessonPaymentAmount, 2) > 0.00) {
                        if ( round($amount, 2) > 0.00) {
                            $lessonPayment = new LessonPayment();
                            $lessonPayment->lessonId    = $lesson->id;
                            $lessonPayment->paymentId   = $this->paymentId;
                            $lessonPayment->amount      = $lessonPaymentAmount;
                            $lessonPayment->enrolmentId = $lesson->enrolment->id;
                            $lessonPayment->receiptId  = $this->receiptId;
                            $lessonPayment->save();
                            $amount -= round($lessonPaymentAmount, 2);
                        } else {
                            break;
                        }
                    }
                }
            }
        }
        if ($groupLessonPayments) {
            foreach ($groupLessonPayments as $groupLessonPayment) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $lesson->courseId])
                    ->customer($this->userId)
                    ->one();
                $groupLesson = Lesson::findOne($groupLessonPayment['id']);
                $groupLessonPaymentAmount = $groupLessonPayment['value'];
                if ($groupLesson->isOwing($enrolment->id)) {
                    if ( round($grouplessonPaymentAmount, 2) > round($groupLesson->getOwingAmount($enrolment->id), 2)) {
                        $grouplessonPaymentAmount = round($groupLesson->getOwingAmount($enrolment->id), 2);
                    }
                    if ( round($grouplessonPaymentAmount, 2) > 0.00) {
                        if ( round($amount, 2) > 0.00) {
                            $lessonPayment = new LessonPayment();
                            $lessonPayment->lessonId    = $groupLesson->id;
                            $lessonPayment->paymentId   = $this->paymentId;
                            $lessonPayment->receiptId  = $this->receiptId;
                            $lessonPayment->amount      = round($grouplessonPaymentAmount, 2);
                            $lessonPayment->enrolmentId = $enrolment->id;
                            $lessonPayment->save();
                            $amount -= round($grouplessonPaymentAmount, 2);
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
