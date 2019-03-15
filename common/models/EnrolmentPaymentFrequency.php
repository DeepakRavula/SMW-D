<?php

namespace common\models;

use Yii;
use yii\base\Model;

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
            
            $startDate      = new \DateTime($startDate);
            $enrolmentLastPaymentCycleEndDate = new \DateTime($this->course->endDate);
            $intervalMonths = $this->diffInMonths($startDate, $enrolmentLastPaymentCycleEndDate);
            $this->deletePaymentCycles();
            $paymentCycleCount = (int) ($intervalMonths / $this->paymentsFrequency->frequencyLength);
            for ($i = 0; $i <= $paymentCycleCount; $i++) {
                if ($i !== 0) {
                    $startDate     = $endDate->modify('First day of next month');
                }
                $paymentCycle              = new PaymentCycle();
                $paymentCycle->enrolmentId = $this->id;
                $paymentCycle->startDate   = $startDate->format('Y-m-d');
                $endDate = $startDate->modify('+' . $this->paymentsFrequency->frequencyLength . ' month, -1 day');
            
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
            $paymentCycles = Paymentcycle::find()
                ->notDeleted()
                ->andWhere(['enrolmentId' => $this->id])
                ->andWhere(['>=', 'startDate', $this->startDate])
                ->all();
            if ($paymentCycles) {       
            foreach ($paymentCycles as $paymentCycle) {
                $paymentCycle->delete();
            }
        }
        return true;
    }

}