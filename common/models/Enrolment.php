<?php

namespace common\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "enrolment".
 *
 * @property string $id
 * @property string $courseId
 * @property string $studentId
 * @property int $isDeleted
 */
class Enrolment extends \yii\db\ActiveRecord
{
    public $studentIds;
	public $endDate;

	const EDIT_RENEWAL = 'renewal';
	const EDIT_LEAVE = 'leave';
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'enrolment';
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
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
            [['courseId'], 'required'],
            [['courseId', 'studentId'], 'integer'],
            [['paymentFrequencyId', 'isDeleted', 'isConfirmed'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'courseId' => 'Course ID',
            'studentId' => 'Student Name',
            'studentIds' => 'Enrolled Student Name',
            'isDeleted' => 'Is Deleted',
            'paymentFrequencyId' => 'Payment Frequency',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\EnrolmentQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\EnrolmentQuery(get_called_class());
    }

    public function notDeleted()
    {
        $this->where(['enrolment.isDeleted' => false]);

        return $this;
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'courseId']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentId']);
    }

	public function getPaymentFrequencyDiscount()
    {
        return $this->hasOne(PaymentFrequencyDiscount::className(), ['paymentFrequencyId' => 'paymentFrequency']);
    }

	public function getVacation()
    {
        return $this->hasOne(Vacation::className(), ['studentId' => 'studentId']);
    }

    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'programId'])
            ->viaTable('course', ['id' => 'courseId']);
    }

    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'courseId']);
    }

    public function isMonthlyPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) self::PAYMENT_FREQUENCY_MONTHLY;
    }

    public function isQuaterlyPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) self::PAYMENT_FREQUENCY_QUARTERLY;
    }

    public function isHalfYearlyPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) self::PAYMENT_FREQUENCY_HALFYEARLY;
    }

    public function isAnnualPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) self::PAYMENT_FREQUENCY_FULL;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->course->program->isGroup() || (!empty($this->rescheduleBeginDate)) || (!$insert)) {
            return true;
        }
        $interval = new \DateInterval('P1D');
        $startDate = $this->course->startDate;
        $endDate = $this->course->endDate;
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $period = new \DatePeriod($start, $interval, $end);
        foreach ($period as $day) {
            if ((int) $day->format('N') === (int) $this->course->day) {
                $professionalDevelopmentDay = clone $day;
                $professionalDevelopmentDay->modify('last day of previous month');
                $professionalDevelopmentDay->modify('fifth '.$day->format('l'));
                if ($day->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
                    continue;
                }
                $lesson = new Lesson();
                $lesson->setAttributes([
                    'courseId' => $this->course->id,
                    'teacherId' => $this->course->teacherId,
                    'status' => Lesson::STATUS_DRAFTED,
                    'date' => $day->format('Y-m-d H:i:s'),
                    'duration' => $this->course->duration,
                    'isDeleted' => false,
                ]);
                $lesson->save();
            }
        }
    }

    public function getLastDateOfPaymentCycle()
    {
        $priorDate             = (new \DateTime())->modify('+15 day');
        $paymentCycleStartDate = \DateTime::createFromFormat('Y-m-d', $priorDate->format('Y-m-1'));
        switch ($this->paymentFrequencyId) {
            case PaymentFrequency::PAYMENT_FREQUENCY_FULL:
                $paymentCycleEndDate = $paymentCycleStartDate->modify('+1 year, -1 day');
                break;
            case PaymentFrequency::PAYMENT_FREQUENCY_HALFYEARLY:
                $paymentCycleEndDate = $paymentCycleStartDate->modify('+6 month, -1 day');
                break;
            case PaymentFrequency::PAYMENT_FREQUENCY_QUARTERLY:
                $paymentCycleEndDate = $paymentCycleStartDate->modify('+3 month, -1 day');
                break;
            case PaymentFrequency::PAYMENT_FREQUENCY_MONTHLY:
                $paymentCycleEndDate = $paymentCycleStartDate->modify('+1 month, -1 day');
                break;
        }

        return $paymentCycleEndDate;
    }

	public function getLastLessonDateOfPaymentCycle()
    {
		$courseDate = new \DateTime($this->course->startDate);
		$startDate = \DateTime::createFromFormat('Y-m-d', $courseDate->format('Y-m-1'));
        switch ($this->paymentFrequencyId) {
            case PaymentFrequency::PAYMENT_FREQUENCY_FULL:
                $endDate = $startDate->modify('+1 year, -1 day');
                break;
            case PaymentFrequency::PAYMENT_FREQUENCY_HALFYEARLY:
                $endDate = $startDate->modify('+6 month, -1 day');
                break;
            case PaymentFrequency::PAYMENT_FREQUENCY_QUARTERLY:
                $endDate = $startDate->modify('+3 month, -1 day');
                break;
            case PaymentFrequency::PAYMENT_FREQUENCY_MONTHLY:
                $endDate = $startDate->modify('+1 month, -1 day');
                break;
        }
		
        return $endDate;
    }

	public function getPaymentFrequency()
	{
		$paymentFrequency = null;
		switch($this->paymentFrequencyId) {
			case PaymentFrequency::PAYMENT_FREQUENCY_FULL :
				$paymentFrequency = 'Annually';
			break;
			case PaymentFrequency::PAYMENT_FREQUENCY_HALFYEARLY :
				$paymentFrequency = 'Semi-Annually';
			break;
			case PaymentFrequency::PAYMENT_FREQUENCY_QUARTERLY :
				$paymentFrequency = 'Quarterly';
			break;
			case PaymentFrequency::PAYMENT_FREQUENCY_MONTHLY :
				$paymentFrequency = 'Monthly';
			break;
		}
		return $paymentFrequency;
	}
}
