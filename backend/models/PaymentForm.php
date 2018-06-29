<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Invoice;
use common\models\Lesson;
use common\models\Payment;
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
    public $creditIds;
    public $invoicePayments;
    public $lessonPayments;
    public $amountToDistribute;
    public $canUseInvoiceCredits;
    public $canUsePaymentCredits;
    public $selectedCreditValue;
    public $paymentId;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_method_id', 'date'], 'required'],
            ['amount', 'validateAmount'],
            [['date', 'amountNeeded', 'invoiceIds', 'canUseInvoiceCredits', 'selectedCreditValue',
                'lessonIds', 'canUseCustomerCredits', 'creditIds', 'amount', 'userId',
                'amountToDistribute', 'invoicePayments', 'lessonPayments','paymentId'], 'safe']
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
        
        $lessonPayments = $this->lessonPayments;
        $invoicePayments = $this->invoicePayments;
        if ($this->creditIds) {
            if ($this->canUseInvoiceCredits) { 
                $creditInvoices = Invoice::findAll($this->creditIds);
                foreach ($creditInvoices as $creditInvoice) {
                    if ($this->invoiceIds) {
                        foreach ($invoices as $i => $invoice) {
                            if ($invoice->isOwing()) {
                                $paymentModel = new Payment();
                                $paymentModel->amount = $invoicePayments[$i];
                                if ($creditInvoice->hasCredit()) {
                                    if ($paymentModel->amount > $creditInvoice->balance) {
                                        $paymentModel->amount = abs($creditInvoice->balance);
                                    }
                                    $invoice->addPayment($creditInvoice, $paymentModel);
                                } else {
                                    break;
                                }
                            }
                        }
                    }
                    if ($this->lessonIds) {
                        foreach ($lessons as $i => $lesson) {
                            if ($lesson->isOwing($lesson->enrolment->id)) {
                                $paymentModel = new Payment();
                                $paymentModel->amount = $lessonPayments[$i];
                                if ($creditInvoice->hasCredit()) {
                                    if ($paymentModel->amount > $creditInvoice->balance) {
                                        $paymentModel->amount = abs($creditInvoice->balance);
                                    }
                                    $lesson->addPayment($creditInvoice, $paymentModel);
                                } else {
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $amount = $this->amount;
            if ($this->invoiceIds) {
                foreach ($invoices as $i => $invoice) {
                    if ($invoice->isOwing()) {
//                        $paymentModel = new Payment();
//                        $paymentModel->amount = $invoicePayments[$i];
                        if ($amount > 0.00) {
//                            if ($paymentModel->amount > $amount) {
//                                $paymentModel->amount = $amount;
//                            }
                            //$invoice->addPayment($customer, $paymentModel);
                            //$amount -= $paymentModel->amount;
                            $invoicePaymentModel = new InvoicePayment();
                            $invoicePaymentModel->invoice_id = $invoice->id;
                            $invoicePaymentModel->payment_id = $this->paymentId;
                            $invoicePaymentModel->amount     = $invoicePayments[$i];
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
//                        $paymentModel = new Payment();
//                        $paymentModel->amount = $lessonPayments[$i];
                        if ($amount > 0.00) {
//                            if ($paymentModel->amount > $amount) {
//                                $paymentModel->amount = $amount;
//                            }
//                                 $amount -= $paymentModel->amount;
//                                 $paymentModel->save();
                                $lessonPayment = new LessonPayment();
                                $lessonPayment->lessonId    = $lesson->id;
                                $lessonPayment->paymentId   = $this->paymentId;
                                $lessonPayment->amount      = $lessonPayments[$i];
                                $lessonPayment->enrolmentId = $lesson->enrolmentId;
                                $lessonPayment->save();
                            //$lesson->addPayment($customer, $paymentModel);
                           
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
        if ($this->amountToDistribute > ($this->selectedCreditValue + $this->amount)) {
            $this->addError($attributes, "Amount mismatched with distributions");
        }
    }
}
