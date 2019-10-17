<?php

namespace common\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\base\Model;
use common\models\PaymentCycle;
use Carbon\Carbon;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

class EnrolmentPaymentFrequency extends \yii\db\ActiveRecord
{
    public $effectiveDate;
    public $needToRenewal;
    public $isAlreadyPosted;

    const CONSOLE_USER_ID  = 727;


    public static function tableName()
    {
        return 'enrolment_payment_frequency';
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId'
            ],
            'audittrail' => [
                'class' => AuditTrailBehavior::className(),
                'consoleUserId' => self::CONSOLE_USER_ID,
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enrolmentId', 'paymentFrequencyId', 'paymentCycleStartDate'], 'required'],
            [['isDeleted', 'createdOn', 'updatedOn', 'createdByUserId', 'updatedByUserId', 'effectiveDate', 'needToRenewal','isAlreadyPosted'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'isDeleted' => 'Is Deleted',
            'paymentFrequencyId' => 'Payment Frequency',
        ];
    }

    public static function find()
    {
        return new \common\models\query\EnrolmentPaymentFrequencyQuery(get_called_class());
    }


    public function resetPaymentCycle()
    {
        $enrolment = Enrolment::find()->andWhere(['id' => $this->enrolmentId])->one();
        
        $month =  Carbon::parse($this->effectiveDate)->format('m');
        $year =  Carbon::parse($this->effectiveDate)->format('Y');
        $day = 01;
        $formatedDate = $day . '-' . $month . '-' . $year;
        $effectiveDate = (new \DateTime($formatedDate))->format('Y-m-d');
        $startDate = Carbon::parse($effectiveDate)->format('Y-m-1');
        $enrolmentLastPaymentCycleEndDate = new \DateTime($enrolment->course->endDate);
        $intervalMonths = $this->diffInMonths(Carbon::parse($startDate), $enrolmentLastPaymentCycleEndDate);
        $this->deletePaymentCycles();
        $lastPaymentCycle = $enrolment->lastPaymentCycle;
        if ($lastPaymentCycle) {
            $lastPaymentCycle->endDate = Carbon::parse($startDate)->modify('Last day of last month')->format('Y-m-d');
            $lastPaymentCycle->save();
            $newPaymentCycle = clone $lastPaymentCycle;
            $lastPaymentCycle->delete();
            $newPaymentCycle->save();
        }
        $paymentCycleCount = (int)($intervalMonths / $enrolment->paymentsFrequency->frequencyLength);
        for ($i = 0; $i <= $paymentCycleCount; $i++) {
            if ($i !== 0) {
                $startDate     = $endDate->modify('First day of next month');
            }
            $paymentCycle              = new PaymentCycle();
            $paymentCycle->enrolmentId = $this->enrolmentId;
            $paymentCycle->startDate   = $startDate;
            $endDate = Carbon::parse($startDate)->modify('+' . $enrolment->paymentsFrequency->frequencyLength . ' month, -1 day');
            $paymentCycle->id          = null;
            $paymentCycle->isNewRecord = true;
            $paymentCycle->endDate     = $endDate->format('Y-m-t');
            if ($enrolmentLastPaymentCycleEndDate->format('Y-m-d') < $paymentCycle->endDate) {
                $paymentCycle->endDate = $enrolmentLastPaymentCycleEndDate->format('Y-m-t');
            }
            if ($enrolmentLastPaymentCycleEndDate->format('Y-m-d') > $paymentCycle->startDate) {
                $paymentCycle->save();
            }
        }
        $enrolment->setDueDate();
    }

    public function deletePaymentCycles()
    {
        $enrolment = Enrolment::find()->andWhere(['id' => $this->enrolmentId])->one();
        $paymentCycles = Paymentcycle::find()
            ->notDeleted()
            ->andWhere(['enrolmentId' => $enrolment->id])
            ->andWhere(['>=', 'payment_cycle.startDate', Carbon::parse($this->effectiveDate)->format('Y-m-1')])
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

        $months = $diff->y * 12 + $diff->m + (int)($diff->d / 30);

        return (int)$months;
    }

    public function setModel($model, $courseDetail)
    {
        $this->enrolmentId = $model->enrolment->id;
        $this->paymentFrequencyId = $model->enrolment->paymentFrequencyId;
        $this->paymentCycleStartDate = (new \DateTime($courseDetail->paymentCycleStartDate))->format('Y-m-d');
        return $this;
    }
}
