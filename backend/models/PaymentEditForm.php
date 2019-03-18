<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Invoice;
use common\models\GroupLesson;
use common\models\Lesson;
use common\models\Payment;
use common\models\User;
use common\models\Enrolment;
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
    public $date;
    public $payment_method_id;
    public $amount;
    public $amountNeeded;
    public $userId;
    public $invoicePayments;
    public $lessonPayments;
    public $groupLessonPayments;
    public $amountToDistribute;
    public $reference;
    public $notes;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['amount', 'number'],
            ['amount', 'validateAmount'],
            [['date', 'selectedCreditValue', 'amount', 'userId', 'amountToDistribute', 
                'invoicePayments', 'lessonPayments', 'paymentId', 'reference', 
                'groupLessonPayments','notes'], 'safe']
        ];
    }

    public function save()
    {
        $lessonPayments = $this->lessonPayments;
        $groupLessonPayments = $this->groupLessonPayments;
        $invoicePayments = $this->invoicePayments;
        
        if ($lessonPayments) {
            foreach ($lessonPayments as $lessonPayment) {
                $lesson = Lesson::findOne($lessonPayment['id']);
                $amount = $lessonPayment['value'];
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
        }

        if ($groupLessonPayments) {
            foreach ($groupLessonPayments as $groupLessonPayment) {
                $groupLesson = GroupLesson::findOne($groupLessonPayment['id']);
                $amount = $groupLessonPayment['value'];
                $payments = $groupLesson->lesson->getPaymentsById($this->paymentId, $groupLesson->enrolment->id);
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
        }

        if ($invoicePayments) {
            foreach ($invoicePayments as $invoicePayment) {
                $invoice = Invoice::findOne($invoicePayment['id']);
                $amount = $invoicePayment['value'];
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
