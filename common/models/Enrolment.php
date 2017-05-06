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
	public $toEmailAddress;
	public $subject;
	public $content;
	public $hasEditable;
	
	const EDIT_RENEWAL = 'renewal';
	const EDIT_LEAVE = 'leave';

	const EVENT_CREATE = 'create';
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
            [['paymentFrequencyId', 'isDeleted', 'isConfirmed', 'hasEditable'], 'safe'],
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
			'toEmailAddress' => 'To',
			'showAllEnrolments' => 'Show All'
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

    public function getPaymentsFrequency()
    {
        return $this->hasOne(PaymentFrequency::className(), ['id' => 'paymentFrequencyId']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentId']);
    }

    public function getPaymentCycles()
    {
        return $this->hasMany(PaymentCycle::className(), ['enrolmentId' => 'id'])
            ->orderBy(['payment_cycle.startDate' => SORT_ASC]);
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

    public function getCurrentPaymentCycle()
    {
        return $this->hasOne(PaymentCycle::className(), ['enrolmentId' => 'id'])
            ->where(['AND',
                ['<=', 'startDate', (new \DateTime())->format('Y-m-d')],
                ['>=', 'endDate', (new \DateTime())->format('Y-m-d')]
            ]);
    }

    public function getNextPaymentCycle()
    {
        $currentPaymentCycleEndDate = new \DateTime($this->currentPaymentCycle->endDate);
        $nextPaymentCycleStartDate  = $currentPaymentCycleEndDate->modify('+1 day');

        return $this->hasOne(PaymentCycle::className(), ['enrolmentId' => 'id'])
            ->where(['AND',
                ['<=', 'startDate', $nextPaymentCycleStartDate->format('Y-m-d')],
                ['>=', 'endDate', $nextPaymentCycleStartDate->format('Y-m-d')]
            ]);
    }

    public function getFirstLesson()
    {
        return $this->hasOne(Lesson::className(), ['courseId' => 'courseId'])
            ->orderBy(['date' => SORT_ASC]);
    }

    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'courseId']);
    }

    public function getFirstPaymentCycle()
    {
        return $this->hasOne(PaymentCycle::className(), ['enrolmentId' => 'id'])
                ->orderBy(['startDate' => SORT_ASC]);
    }

    public function getFirstUnInvoicedProFormaPaymentCycle()
    {
        foreach ($this->paymentCycles as $paymentCycle) {
            if (!$paymentCycle->hasProFormaInvoice()) {
                return $paymentCycle;
            }
        }

        return null;
    }
    
    public function getFirstUnPaidProFormaPaymentCycle()
    {
        foreach ($this->paymentCycles as $paymentCycle) {
            if (!$paymentCycle->hasProFormaInvoice()) {
                return $paymentCycle;
            } else if (!$paymentCycle->proformaInvoice->isPaid()) {
                return $paymentCycle;
            }
        }

        return null;
    }

    public function getUnInvoicedProFormaPaymentCycles()
    {
        $models = [];
        foreach ($this->paymentCycles as $paymentCycle) {
            if (!$paymentCycle->hasProFormaInvoice()) {
                $models[] = $paymentCycle;
            }
        }

        return $models;
    }
    
    public function getUnPaidProFormaPaymentCycles()
    {
        $models = [];
        foreach ($this->paymentCycles as $paymentCycle) {
            if (!$paymentCycle->hasProFormaInvoice()) {
                $models[] = $paymentCycle;
            } else if (!$paymentCycle->proformaInvoice->isPaid()) {
                $models[] = $paymentCycle;
            }
        }

        return $models;
    }

    public function getlastPaymentCycle()
    {
        return $this->hasOne(PaymentCycle::className(), ['enrolmentId' => 'id'])
                ->orderBy(['endDate' => SORT_DESC]);
    }

    public function isMonthlyPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) self::LENGTH_MONTHLY;
    }

    public function isQuaterlyPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) self::LENGTH_QUARTERLY;
    }

    public function isHalfYearlyPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) self::LENGTH_HALFYEARLY;
    }

    public function isAnnualPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) self::LENGTH_FULL;
    }

	public function beforeSave($insert) {
		if($insert) {
			$this->isDeleted = false;
			$this->isConfirmed = false;
		}
		return parent::beforeSave($insert);
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

	public static function getPaymentFrequencies()
    {
        return [
            self::STATUS_COMPLETED => Yii::t('common', 'Completed'),
            self::STATUS_SCHEDULED => Yii::t('common', 'Scheduled'),
        ];
    }
	
    public function getPaymentFrequency()
	{
		$paymentFrequency = null;
		switch($this->paymentFrequencyId) {
			case PaymentFrequency::LENGTH_FULL :
				$paymentFrequency = 'Annually';
			break;
			case PaymentFrequency::LENGTH_HALFYEARLY :
				$paymentFrequency = 'Semi-Annually';
			break;
			case PaymentFrequency::LENGTH_QUARTERLY :
				$paymentFrequency = 'Quarterly';
			break;
			case PaymentFrequency::LENGTH_MONTHLY :
				$paymentFrequency = 'Monthly';
			break;
		}
		return $paymentFrequency;
	}

    public function deleteUnPaidProformaPaymentCycles()
    {
        foreach ($this->unPaidProFormaPaymentCycles as $model) {
            $model->delete();
        }
    }

    public function resetPaymentCycle()
    {
        if (!empty($this->firstUnPaidProFormaPaymentCycle)) {
            $startDate = \DateTime::createFromFormat('Y-m-d',
                $this->firstUnPaidProFormaPaymentCycle->startDate);
            $enrolmentLastPaymentCycleEndDate = \DateTime::createFromFormat('Y-m-d',
                $this->lastPaymentCycle->endDate);
            $interval = $startDate->diff($enrolmentLastPaymentCycleEndDate);
            $this->deleteUnPaidProformaPaymentCycles();
            for ($i = 0; $i <= $interval->format('%m') / $this->paymentsFrequency->frequencyLength; $i++) {
                if ($i !== 0) {
                    $startDate     = $endDate->modify('First day of next month');
                }
                $paymentCycle              = new PaymentCycle();
                $paymentCycle->enrolmentId = $this->id;
                $paymentCycle->startDate   = $startDate->format('Y-m-d');
                switch ($this->paymentFrequencyId) {
                    case PaymentFrequency::LENGTH_FULL:
                        $endDate = $startDate->modify('+1 year, -1 day');
                        break;
                    case PaymentFrequency::LENGTH_HALFYEARLY:
                        $endDate = $startDate->modify('+6 month, -1 day');
                        break;
                    case PaymentFrequency::LENGTH_QUARTERLY:
                        $endDate = $startDate->modify('+3 month, -1 day');
                        break;
                    case PaymentFrequency::LENGTH_MONTHLY:
                        $endDate = $startDate->modify('+1 month, -1 day');
                        break;
                }

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
    }

    public function setPaymentCycle()
    {
        $enrolmentStartDate      = \DateTime::createFromFormat('Y-m-d H:i:s', $this->firstLesson->date);
        $paymentCycleStartDate   = \DateTime::createFromFormat('Y-m-d', $enrolmentStartDate->format('Y-m-1'));
        for ($i = 0; $i < 12 / $this->paymentsFrequency->frequencyLength; $i++) {
            if ($i !== 0) {
                $paymentCycleStartDate     = $endDate->modify('First day of next month');
            }
            $paymentCycle              = new PaymentCycle();
            $paymentCycle->enrolmentId = $this->id;
            $paymentCycle->startDate   = $paymentCycleStartDate->format('Y-m-d');
            switch ($this->paymentFrequencyId) {
                case PaymentFrequency::LENGTH_FULL:
                    $endDate = $paymentCycleStartDate->modify('+1 year, -1 day');
                    break;
                case PaymentFrequency::LENGTH_HALFYEARLY:
                    $endDate = $paymentCycleStartDate->modify('+6 month, -1 day');
                    break;
                case PaymentFrequency::LENGTH_QUARTERLY:
                    $endDate = $paymentCycleStartDate->modify('+3 month, -1 day');
                    break;
                case PaymentFrequency::LENGTH_MONTHLY:
                    $endDate = $paymentCycleStartDate->modify('+1 month, -1 day');
                    break;
            }

            $paymentCycle->id          = null;
            $paymentCycle->isNewRecord = true;
            $paymentCycle->endDate     = $endDate->format('Y-m-d');
            $paymentCycle->save();
        }
    }

	public function sendEmail()
    {
		if(!empty($this->toEmailAddress)) {
			$content = [];
			foreach($this->toEmailAddress as $email) {
				$subject                      = $this->subject;
				$content[] = Yii::$app->mailer->compose('lesson-schedule', [
                	'content' => $this->content,
            	])
				->setFrom(\Yii::$app->params['robotEmail'])
				->setReplyTo($this->course->location->email)
				->setTo($email)
				->setSubject($subject);
			}
			return Yii::$app->mailer->sendMultiple($content);
		}
	}
}
