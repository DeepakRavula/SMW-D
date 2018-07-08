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
class PaymentEditForm extends Model
{
    public $paymentId;
    public $invoiceIds;
    public $date;
    public $payment_method_id;
    public $amount;
    public $amountNeeded;
    public $userId;
    public $lessonIds;
    public $invoicePayments;
    public $lessonPayments;
    public $amountToDistribute;
    public $reference;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_method_id', 'date'], 'required'],
            ['amount', 'validateAmount'],
            [['date', 'invoiceIds', 'selectedCreditValue', 'lessonIds', 'amount', 'userId', 'amountToDistribute', 
                'invoicePayments', 'lessonPayments', 'paymentId', 'reference'], 'safe']
        ];
    }

    public function save()
    {
        $invoices = Invoice::find()
            ->where(['id' => $this->invoiceIds])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $lessons = Lesson::find()
            ->where(['id' => $this->lessonIds])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $lessonPayments = $this->lessonPayments;
        $invoicePayments = $this->invoicePayments;
        
        foreach ($lessons as $i => $lesson) {
            $amount = $lessonPayments[$i];
            $payments = $lesson->getPaymentsById($this->paymentId);
            foreach ($payments as $i => $lessonPayment) {
                if ($i == 0) {
                    if ($amount == 0) {
                        $lessonPayment->delete();
                    } else {
                        $lessonPayment->amount = $amount;
                        $lessonPayment->save();
                    }
                } else {
                    $lessonPayment->delete();
                }
            }
        }

        foreach ($invoices as $i => $invoice) {
            $amount = $invoicePayments[$i];
            $payments = $invoice->getPaymentsById($this->paymentId);
            foreach ($payments as $i => $invoicePayment) {
                if ($i == 0) {
                    if ($amount == 0) {
                        $invoicePayment->delete();
                    } else {
                        $invoicePayment->amount = $amount;
                        $invoicePayment->save();
                    }
                } else {
                    $invoicePayment->delete();
                }
            }
        }

        return true;
    }

    public function validateAmount($attributes)
    {
        if (round($this->amountToDistribute, 2) > round($this->amount, 2)) {
            $this->addError($attributes, "Amount mismatched with distributions");
        }
        if (round($this->amount, 2) < 0.01) {
            $this->addError($attributes, "Amount can't be empty");
        }
    }
}
