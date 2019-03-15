<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\PaymentCycle;

class EnrolmentPaymentFrequency extends Model
{ 
    public $enrolmentId;
    public $paymentFrequencyId;
        
    public function rules()
    {
        return [
            [['enrolmentId', 'paymentFrequencyId'], 'safe' ],
        ];
    }

    public function resetPaymentCycle()
    {
            $enrolment = Enrolment::find()->andWhere(['id' => $this->enrolmentId])->one();
            $startDate      = new \DateTime($enrolment->course->startDate);
            $enrolmentLastPaymentCycleEndDate = new \DateTime($enrolment->course->endDate);
            $intervalMonths = $this->diffInMonths($startDate, $enrolmentLastPaymentCycleEndDate);
            $this->deletePaymentCycles();
            $paymentCycleCount = (int) ($intervalMonths / $enrolment->paymentsFrequency->frequencyLength);
            for ($i = 0; $i <= $paymentCycleCount; $i++) {
                if ($i !== 0) {
                    $startDate     = $endDate->modify('First day of next month');
                }
                $paymentCycle              = new PaymentCycle();
                $paymentCycle->enrolmentId = $this->enrolmentId;
                $paymentCycle->startDate   = $startDate->format('Y-m-d');
                $endDate = $startDate->modify('+' . $enrolment->paymentsFrequency->frequencyLength . ' month, -1 day');
            
                $paymentCycle->id          = null;
                $paymentCycle->isNewRecord = true;
                $paymentCycle->endDate     = $endDate->format('Y-m-d');
                if ($enrolmentLastPaymentCycleEndDate->format('Y-m-d') < $paymentCycle->endDate) {
                    $paymentCycle->endDate = $enrolmentLastPaymentCycleEndDate->format('Y-m-d');
                }
                if ($enrolmentLastPaymentCycleEndDate->format('Y-m-d') > $paymentCycle->startDate) {
                    $paymentCycle->save();
                }
            }
    }

    public function deletePaymentCycles()
    {
            $enrolment = Enrolment::find()->andWhere(['id' => $this->enrolmentId])->one();
            $paymentCycles = Paymentcycle::find()
                ->notDeleted()
                ->andWhere(['enrolmentId' => $enrolment->id])
                ->all();
            if ($paymentCycles) {       
            foreach ($paymentCycles as $paymentCycle) {
                $paymentCycle->delete();
            }
        }
        return true;
    }

    public function diffInMonths($date1, $date2)
    {
        $diff =  $date1->diff($date2);

        $months = $diff->y * 12 + $diff->m + (int) ($diff->d / 30);

        return (int) $months;
    }

}