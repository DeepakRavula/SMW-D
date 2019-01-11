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
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

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

    public $date;
    public $prId;
    public $payment_method_id;
    public $amount;
    public $amountNeeded;
    public $lessonId;
    public $userId;
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
            [['date', 'amountNeeded', 'canUseInvoiceCredits', 'selectedCreditValue', 'canUsePaymentCredits', 
                'invoiceCreditIds', 'amount', 'userId', 'amountToDistribute', 'invoicePayments', 'lessonPayments', 
                'paymentId', 'paymentCredits', 'invoiceCredits', 'reference', 'paymentCreditIds', 'prId',
                'groupLessonIds', 'groupLessonPayments', 'receiptId', 'payment_method_id', 'notes'], 'safe']
        ];
    }

    public function getLessonIds()
    {
        $lessonIds = null;
        if ($this->lessonPayments) {
            $lessonIds = ArrayHelper::getColumn($this->lessonPayments, 'id');
        }
        return $lessonIds;
    }

    public function getInvoiceIds()
    {
        $invoiceIds = null;
        if ($this->invoicePayments) {
            $invoiceIds = ArrayHelper::getColumn($this->invoicePayments, 'id');
        }
        return $invoiceIds;
    }

    public function getGroupLessonIds()
    {
        $lessonIds = null;
        if ($this->groupLessonPayments) {
            $lessonIds = ArrayHelper::getColumn($this->groupLessonPayments, 'id');
        }
        return $lessonIds;
    }

    public function save()
    {
        $customer = User::findOne($this->userId);
        $paymentCredits = $this->paymentCredits;
        $invoiceCredits = $this->invoiceCredits;
        $lessonPayments = $this->lessonPayments;
        $groupLessonPayments = $this->groupLessonPayments;
        $invoicePayments = $this->invoicePayments;
        if ($invoiceCredits) {
            if ($this->canUseInvoiceCredits) { 
                foreach ($invoiceCredits as $j => $invoiceCredit) {
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
                                            $invoicePayments[$i]['value'] -= round($paymentModel->amount, 2);
                                            $creditInvoiceAmount = $invoicePayments[$i]['value'];
                                            $invoiceCredits[$j]['value'] -= round($paymentModel->amount, 2);
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
                            foreach ($lessonPayments as $i => $lessonPayment) {
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
                                            $lessonPayments[$i]['value'] -= round($paymentModel->amount, 2);
                                            $creditInvoiceAmount = $lessonPayments[$i]['value'];
                                            $invoiceCredits[$j]['value'] -= round($paymentModel->amount, 2);
                                            $lesson->addPayment($creditInvoice, $paymentModel);
                                        } else {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if ($groupLessonPayments) {
                            foreach ($groupLessonPayments as $i => $groupLessonPayment) {
                                $groupLesson = Lesson::findOne($groupLessonPayment['id']);
                                $enrolment = Enrolment::find()
                                    ->notDeleted()
                                    ->isConfirmed()
                                    ->andWhere(['courseId' => $groupLesson->courseId])
                                    ->customer($this->userId)
                                    ->one();
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
                                            $groupLessonPayments[$i]['value'] -=round($paymentModel->amount, 2);
                                            $creditInvoiceAmount = $groupLessonPayments[$i]['value'];
                                            $invoiceCredits[$j]['value'] -= round($paymentModel->amount, 2);
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
                foreach ($paymentCredits as $j => $paymentCredit) {
                    $creditPayment = Payment::findOne($paymentCredit['id']);
                    $creditPaymentAmount = $paymentCredit['value'];
                    if ($creditPayment->hasCredit()) {
                        if (round($creditPaymentAmount, 2) > round(abs($creditPayment->creditAmount), 2)) {
                            $creditPaymentAmount = round(abs($creditPayment->creditAmount), 2);
                        }
                        if ($invoicePayments) {
                            foreach ($invoicePayments as $i => $invoicePayment) {
                                $invoice = Invoice::findOne($invoicePayment['id']);
                                $invoicePaymentAmount = $invoicePayment['value'];
                                if ($invoice->isOwing()) {
                                    if (round($invoicePaymentAmount, 2) > round($invoice->balance, 2)) {
                                        $invoicePaymentAmount = round($invoice->balance, 2);
                                    }
                                    if (round($creditPaymentAmount, 2) > 0.00) {
                                        if (round($invoicePaymentAmount, 2) > round($creditPaymentAmount, 2)) {
                                            $amountToPay = round($creditPaymentAmount, 2);                                
                                        } else {
                                            $amountToPay = round($invoicePaymentAmount, 2);
                                        }
                                        $invoicePayments[$i]['value'] -= round($amountToPay, 2);
                                        $creditPaymentAmount = $invoicePayments[$i]['value'];
                                        $paymentCredits[$j]['value'] -= round($amountToPay, 2);
                                        $invoicePaymentModel = new InvoicePayment();
                                        $invoicePaymentModel->invoice_id = $invoice->id;
                                        $invoicePaymentModel->payment_id = $creditPayment->id;
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
                            foreach ($lessonPayments as $i => $lessonPayment) {
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
                                        $lessonPayments[$i]['value'] -= round($amountToPay, 2);
                                        $creditPaymentAmount = $lessonPayments[$i]['value'];
                                        $paymentCredits[$j]['value'] -= round($amountToPay, 2);
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
                            foreach ($groupLessonPayments as $i => $groupLessonPayment) {
                                $groupLesson = Lesson::findOne($groupLessonPayment['id']);
                                $enrolment = Enrolment::find()
                                    ->notDeleted()
                                    ->isConfirmed()
                                    ->andWhere(['courseId' => $groupLesson->courseId])
                                    ->customer($this->userId)
                                    ->one();
                                $groupLessonPaymentAmount = $groupLessonPayment['value'];
                                if ($groupLesson->isOwing($enrolment->id)) {
                                    if (round($groupLessonPaymentAmount, 2) > round($groupLesson->getOwingAmount($enrolment->id), 2)) {
                                        $groupLessonPaymentAmount = round($groupLesson->getOwingAmount($enrolment->id), 2);
                                    }
                                    if (round($creditPaymentAmount, 2) > 0.00) {
                                        if (round($groupLessonPaymentAmount, 2) > round($creditPaymentAmount, 2)) {
                                            $amountToPay = round($creditPaymentAmount, 2);
                                        } else {
                                            $amountToPay = round($groupLessonPaymentAmount, 2);
                                        }
                                        $groupLessonPayments[$i]['value'] -= round($amountToPay, 2);
                                        $creditPaymentAmount = $groupLessonPayments[$i]['value'];
                                        $paymentCredits[$j]['value'] -=  round($amountToPay, 2);
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
                $groupLesson = Lesson::findOne($groupLessonPayment['id']);
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $groupLesson->courseId])
                    ->customer($this->userId)
                    ->one();
                $groupLessonPaymentAmount = $groupLessonPayment['value'];
                if ($groupLesson->isOwing($enrolment->id)) {
                    if ( round($groupLessonPaymentAmount, 2) > round($groupLesson->getOwingAmount($enrolment->id), 2)) {
                        $groupLessonPaymentAmount = round($groupLesson->getOwingAmount($enrolment->id), 2);
                    }
                    if ( round($groupLessonPaymentAmount, 2) > 0.00) {
                        if ( round($amount, 2) > 0.00) {
                            $lessonPayment = new LessonPayment();
                            $lessonPayment->lessonId    = $groupLesson->id;
                            $lessonPayment->paymentId   = $this->paymentId;
                            $lessonPayment->receiptId  = $this->receiptId;
                            $lessonPayment->amount      = round($groupLessonPaymentAmount, 2);
                            $lessonPayment->enrolmentId = $enrolment->id;
                            $lessonPayment->save();
                            $amount -= round($groupLessonPaymentAmount, 2);
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

    public function getUsedCredit()
    {
        $paymentCredits = $this->paymentCredits;
        $invoiceCredits = $this->invoiceCredits;
        $paymentId = $this->paymentId;
        $amount = $this->amount;
        $results = [];
        if (!empty($paymentCredits)) {                
            foreach ($paymentCredits as $paymentCredit) {
                $creditPayment = Payment::findOne(['id' => $paymentCredit['id']]);
                $results[] = [
                    'id' => $creditPayment->id,
                    'type' => 'Payment Credit',
                    'reference' => $creditPayment->reference,
                    'amount' => round($creditPayment->amount, 2),
                    'method' => $creditPayment->paymentMethod->name,
                    'amountUsed' => round($paymentCredit['value'], 2),
                ];
            }  
        }
        if  (!empty($invoiceCredits)) { 
            foreach ($invoiceCredits as $invoiceCredit) {
                $creditInvoice = Invoice::findOne(['id' => $invoiceCredit['id']]);
                $results[] = [
                    'id' => $creditInvoice->id,
                    'type' => 'Invoice Credit',
                    'reference' => $creditInvoice->getInvoiceNumber(),
                    'amount' => '',
                    'method' => '',
                    'amountUsed' => round($invoiceCredit['value'], 2),
                ];
            }
        } 
        $paymentNew = Payment::findOne(['id' => $paymentId]);
        if (!empty($paymentNew)) {
            $results[] = [
                'id' => $paymentId,
                'type' => 'Payment',
                'reference' => !empty($paymentNew->reference) ? $paymentNew->reference : null,
                'amount' => $paymentNew->amount,
                'method' => $paymentNew->paymentMethod->name,
                'amountUsed' => $amount,
            ]; 
        }
        $paymentsLineItemsDataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'sort' => [
                'attributes' => ['id', 'type', 'reference', 'amount', 'amountUsed']
            ],
            'pagination' => false
        ]);
        return $paymentsLineItemsDataProvider;
    }

    public function getInvoicesPaid()
    {
        $invoicePayments = $this->invoicePayments;
        $dataProvider = null;
        if ($invoicePayments) {
            $invoiceIds = ArrayHelper::getColumn($invoicePayments, 'id');
            $invoices = Invoice::find()
                ->andWhere(['id' => $invoiceIds])
                ->orderBy(['id' => SORT_ASC]);

            $dataProvider = new ActiveDataProvider([
                'query' => $invoices,
                'pagination' => false 
            ]);
        }
        return $dataProvider;
    }

    public function getGroupLessonsPaid()
    {
        $groupLessonPayments = $this->groupLessonPayments;
        $dataProvider = null;
        if ($groupLessonPayments) {
            $lessonIds = ArrayHelper::getColumn($groupLessonPayments, 'id');
            $lessons = Lesson::find()
                ->andWhere(['id' => $lessonIds])
                ->orderBy(['lesson.id' => SORT_ASC]);

            $dataProvider = new ActiveDataProvider([
                'query' => $lessons,
                'pagination' => false 
            ]);
        }
        return $dataProvider;
    }

    public function getLessonsPaid()
    {
        $lessonPayments = $this->lessonPayments;
        $dataProvider = null;
        if ($lessonPayments) {
            $lessonIds = ArrayHelper::getColumn($lessonPayments, 'id');
            $lessons = Lesson::find()
                ->andWhere(['id' => $lessonIds])
                ->orderBy(['lesson.id' => SORT_ASC]);

            $dataProvider = new ActiveDataProvider([
                'query' => $lessons,
                'pagination' => false 
            ]);
        }
        return $dataProvider;
    }
}
