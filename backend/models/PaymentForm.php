<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Invoice;
use common\models\Lesson;
use common\models\Payment;
use common\models\User;

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
    public $userId;
    public $lessonIds;
    public $creditIds;
    public $canUseInvoiceCredits;
    public $canUseCustomerCredits;
    public $selectedCreditValue;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_method_id', 'date'], 'required'],
            ['amount', 'validateAmount'],
            [['date', 'amountNeeded', 'invoiceIds', 'canUseInvoiceCredits', 'selectedCreditValue',
                'lessonIds', 'canUseCustomerCredits', 'creditIds', 'amount', 'userId'], 'safe']
        ];
    }

    public function save()
    {
        $customer = User::findOne($this->userId);
        if ($this->creditIds) {
            if ($this->canUseCustomerCredits) {
                if ($this->invoiceIds) {
                    $invoices = Invoice::findAll($this->invoiceIds);
                    foreach ($invoices as $invoice) {
                        if ($invoice->isOwing()) {
                            $paymentModel = new Payment();
                            $paymentModel->amount = $invoice->balance;
                            if ($customer->hasCustomerCredit()) {
                                if ($paymentModel->amount > $customer->creditAmount) {
                                    $paymentModel->amount = $customer->creditAmount;
                                }
                                $invoice->addPayment($customer, $paymentModel);
                            } else {
                                break;
                            }
                        }
                    }
                }
                if ($this->lessonIds) {
                    $lessons = Lesson::findAll($this->lessonIds);
                    foreach ($lessons as $lesson) {
                        if ($lesson->isOwing($lesson->enrolment->id)) {
                            $paymentModel = new Payment();
                            $paymentModel->amount = $lesson->getOwingAmount($lesson->enrolment->id);
                            if ($customer->hasCustomerCredit()) {
                                if ($paymentModel->amount > $customer->creditAmount) {
                                    $paymentModel->amount = $customer->creditAmount;
                                }
                                $lesson->addPayment($customer, $paymentModel);
                            } else {
                                break;
                            }
                        }
                    }
                }
            }
            if ($this->canUseInvoiceCredits) {
                $creditInvoices = $this->getCustomerCreditInvoices($this->userId);
                foreach ($creditInvoices as $creditInvoice) {
                    if ($this->invoiceIds) {
                        $invoices = Invoice::findAll($this->invoiceIds);
                        foreach ($invoices as $invoice) {
                            if ($invoice->isOwing()) {
                                $paymentModel = new Payment();
                                $paymentModel->amount = $invoice->balance;
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
                        $lessons = Lesson::findAll($this->lessonIds);
                        foreach ($lessons as $lesson) {
                            if ($lesson->isOwing($lesson->enrolment->id)) {
                                $paymentModel = new Payment();
                                $paymentModel->amount = $lesson->getOwingAmount($lesson->enrolment->id);
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
                $invoices = Invoice::findAll($this->invoiceIds);
                foreach ($invoices as $invoice) {
                    if ($invoice->isOwing()) {
                        $paymentModel = new Payment();
                        $paymentModel->amount = $invoice->balance;
                        if ($amount > 0.00) {
                            if ($paymentModel->amount > $amount) {
                                $paymentModel->amount = $amount;
                            }
                            $invoice->addPayment($customer, $paymentModel);
                            $amount -= $paymentModel->amount;
                        } else {
                            break;
                        }
                    }
                }
            }
            if ($this->lessonIds) {
                $lessons = Lesson::findAll($this->lessonIds);
                foreach ($lessons as $lesson) {
                    if ($lesson->isOwing($lesson->enrolment->id)) {
                        $paymentModel = new Payment();
                        $paymentModel->amount = $lesson->getOwingAmount($lesson->enrolment->id);
                        if ($amount > 0.00) {
                            if ($paymentModel->amount > $amount) {
                                $paymentModel->amount = $amount;
                            }
                            $lesson->addPayment($customer, $paymentModel);
                            $amount -= $paymentModel->amount;
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
    }
}
