<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\PaymentCycle;
use Carbon\Carbon;

class EnrolmentPaymentFrequency extends Model
{
    public $enrolmentId;
    public $paymentFrequencyId;
    public $effectiveDate;

    public function rules()
    {
        return [
            [['enrolmentId', 'paymentFrequencyId', 'effectiveDate'], 'safe'],
        ];
    }

    public function resetDueDates()
    {
        $enrolment = Enrolment::find()->andWhere(['id' => $this->enrolmentId])->one();
        $effectiveDate = Carbon::parse($this->effectiveDate)->format('Y-m-1');
        $paymentFrequency = $enrolment->paymentFrequencyId;
        $startDate = Carbon::parse($enrolment->course->startDate);
        $diffInMonths = $this->diffInMonths(Carbon::parse($effectiveDate), Carbon::parse($enrolment->course->endDate));

        $startDate = carbon::parse($effectiveDate)->format('Y-m-d');
        for ($i = 0; $i <= $diffInMonths / $paymentFrequency; $i++) {
            $fromDate = Carbon::parse($startDate)->format('Y-m-1');
            $paymentFrequencyDays = ($paymentFrequency) * 30;
            $toDate = Carbon::parse($fromDate)->modify('+' . $paymentFrequencyDays . 'days')->modify('last day of this month')->format('Y-m-d');
            $lessons = Lesson::find()
                ->enrolment($enrolment->id)
                ->notCanceled()
                ->isConfirmed()
                ->notDeleted()
                ->between(carbon::parse($fromDate), carbon::parse($toDate))
                ->all();
            $dueDate = Carbon::parse($fromDate)->modify('- 15days')->format('Y-m-d');
            if ($lessons) {
                foreach ($lessons as $lesson) {
                    $lesson->updateAttributes(['dueDate' => $dueDate]);
                }
            }
            $startDate = Carbon::parse($toDate)->modify('+1days')->format('Y-m-d');
        }
    }

    public function diffInMonths($date1, $date2)
    {
        $diff =  $date1->diff($date2);

        $months = $diff->y * 12 + $diff->m + (int)($diff->d / 30);

        return (int)$months;
    }
}

