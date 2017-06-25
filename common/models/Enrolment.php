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
	public $programName;
	public $enrolmentCount;
    public $userName;

    const EVENT_CREATE = 'create';
    const EVENT_GROUP='group-course-enroll';
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
				'replaceRegularDelete' => true
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

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'courseId']);
    }
	public function getCourseSchedule()
    {
        return $this->hasOne(CourseSchedule::className(), ['courseId' => 'id'])
			->via('course');
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

    public function hasProFormaInvoice()
    {
        return !empty($this->proFormaInvoice);
    }

    public function getProFormaInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->via('invoiceLineItems')
            ->onCondition(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
    }

    public function hasInvoice($lessonId)
    {
        return !empty($this->getInvoice($lessonId));
    }

    public function getInvoice($lessonId)
    {
        $enrolmentId = $this->id;
        return Invoice::find()
            ->notDeleted()
            ->invoice()
            ->enrolmentLesson($lessonId, $enrolmentId)
            ->one();
    }

    public function getInvoiceLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
            ->via('invoiceItemsEnrolment')
            ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_GROUP_LESSON]);
    }

    public function getInvoiceItemsEnrolment()
    {
        return $this->hasMany(InvoiceItemEnrolment::className(), ['enrolmentId' => 'id']);
    }

    public function getCurrentPaymentCycle()
    {
        $currentPaymentCycle = PaymentCycle::find()
            ->where(['enrolmentId' => $this->id])
            ->andWhere(['AND',
                ['<=', 'startDate', (new \DateTime())->format('Y-m-d')],
                ['>=', 'endDate', (new \DateTime())->format('Y-m-d')]
            ])
            ->one();
        if (!empty($currentPaymentCycle)) {
            return $currentPaymentCycle;
        } else {
            return $this->firstPaymentCycle;
        }
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

    public function getCourseCount()
    {
        return Lesson::find()
                ->notDeleted()
                ->where(['courseId' => $this->courseId])
                ->count('id');
    }

    public function getFirstUnPaidProFormaPaymentCycle()
    {
        foreach ($this->paymentCycles as $paymentCycle) {
            if (!$paymentCycle->hasProFormaInvoice()) {
                return $paymentCycle;
            } else if (!$paymentCycle->proFormaInvoice->isPaid() &&
                !$paymentCycle->proFormaInvoice->hasPayments()) {
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
            } else if (!$paymentCycle->proFormaInvoice->isPaid() &&
                !$paymentCycle->proFormaInvoice->hasPayments()) {
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
			$lessonCount = Lesson::find()
				->andWhere(['courseId' => $this->courseId, 'status' => Lesson::STATUS_DRAFTED])
				->count();
			$checkDay = (int) $day->format('N') === (int) $this->courseSchedule->day;
			$checkLessonCount = (int)$lessonCount < Lesson::MAXIMUM_LIMIT; 
			if ($checkDay && $checkLessonCount) {
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
					'duration' => $this->courseSchedule->duration,
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
            case PaymentFrequency::LENGTH_EVERY_TWO_MONTH:
                $paymentFrequency = 'Bi-Monthly';
            break;
            case PaymentFrequency::LENGTH_EVERY_FOUR_MONTH:
                $paymentFrequency = 'Every Four Month';
                break;
            case PaymentFrequency::LENGTH_EVERY_FIVE_MONTH:
                $paymentFrequency = 'Every Five Month';
                break;
            case PaymentFrequency::LENGTH_EVERY_SEVEN_MONTH:
                $paymentFrequency = 'Every Seven Month';
                break;
            case PaymentFrequency::LENGTH_EVERY_EIGHT_MONTH:
                $paymentFrequency = 'Every Eight Month';
                break;
            case PaymentFrequency::LENGTH_EVERY_NINE_MONTH:
                $paymentFrequency = 'Every Nine Month';
                break;
            case PaymentFrequency::LENGTH_EVERY_TEN_MONTH:
                $paymentFrequency = 'Every Ten Month';
                break;
            case PaymentFrequency::LENGTH_EVERY_ELEVEN_MONTH:
                $paymentFrequency = 'Every Eleven Month';
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

    public function diffInMonths($date1, $date2)
    {
        $diff =  $date1->diff($date2);

        $months = $diff->y * 12 + $diff->m + (int) ($diff->d / 30);

        return (int) $months;
    }

    public function resetPaymentCycle()
    {
        if (!empty($this->firstUnPaidProFormaPaymentCycle)) {            
            $startDate = \DateTime::createFromFormat('Y-m-d',
                $this->firstUnPaidProFormaPaymentCycle->startDate);        
            $enrolmentLastPaymentCycleEndDate = \DateTime::createFromFormat('Y-m-d',
                $this->lastPaymentCycle->endDate);
            $intervalMonths = $this->diffInMonths($startDate, $enrolmentLastPaymentCycleEndDate);
            $this->deleteUnPaidProformaPaymentCycles();
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
    }

    public function setPaymentCycle()
    {
        $enrolmentStartDate      = \DateTime::createFromFormat('Y-m-d H:i:s', $this->firstLesson->date);
        $paymentCycleStartDate   = \DateTime::createFromFormat('Y-m-d', $enrolmentStartDate->format('Y-m-1'));
        for ($i = 0; $i <= (int) 12 / $this->paymentsFrequency->frequencyLength; $i++) {
            if ($i !== 0) {
                $paymentCycleStartDate     = $endDate->modify('First day of next month');
            }
            $paymentCycle              = new PaymentCycle();
            $paymentCycle->enrolmentId = $this->id;
            $paymentCycle->startDate   = $paymentCycleStartDate->format('Y-m-d');
            $endDate = $paymentCycleStartDate->modify('+' . $this->paymentsFrequency->frequencyLength . ' month, -1 day');
            $paymentCycle->id          = null;
            $paymentCycle->isNewRecord = true;
            $paymentCycle->endDate     = $endDate->format('Y-m-d');
            if ((new \DateTime($this->course->endDate))->format('Y-m-d') < $paymentCycle->endDate) {
                $paymentCycle->endDate = (new \DateTime($this->course->endDate))->format('Y-m-t');
            }
            if ((new \DateTime($this->course->endDate))->format('Y-m-d') > $paymentCycle->startDate) {
                $paymentCycle->save();
            }
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

    public function hasExplodedLesson()
    {
        $courseId = $this->courseId;
        $locationId = $this->course->locationId;
        $lessonSplits = LessonSplit::find()
                    ->unusedSplits($courseId, $locationId)
                    ->all();

        return !empty($lessonSplits);
    }

    public function createProFormaInvoice()
    {
        $locationId = $this->student->customer->userLocation->location_id;
        $user = User::findOne(['id' => Yii::$app->user->id]);
        $invoice = new Invoice();
        $invoice->on(Invoice::EVENT_CREATE, [new InvoiceLog(), 'create']);
        $invoice->userName = $user->publicIdentity;
        $invoice->user_id = $this->student->customer->id;
        $invoice->location_id = $locationId;
        $invoice->dueDate = (new \DateTime($this->firstLesson->date))->format('Y-m-d');
        $invoice->type = INVOICE::TYPE_PRO_FORMA_INVOICE;
        $invoice->createdUserId = Yii::$app->user->id;
        $invoice->updatedUserId = Yii::$app->user->id;
        if (!$invoice->save()) {
            Yii::error('Create Invoice: ' . \yii\helpers\VarDumper::dumpAsString($invoice->getErrors()));
        }
        $invoiceLineItem = $invoice->addGroupProFormaLineItem($this);
        if (!$invoiceLineItem->save()) {
            Yii::error('Create Invoice Line Item: ' . \yii\helpers\VarDumper::dumpAsString($invoiceLineItem->getErrors()));
        } else {
            $invoiceItemLesson = new InvoiceItemEnrolment();
            $invoiceItemLesson->enrolmentId    = $this->id;
            $invoiceItemLesson->invoiceLineItemId    = $invoiceLineItem->id;
            $invoiceItemLesson->save();
        }
        if (!$invoice->save()) {
            Yii::error('Create Invoice: ' . \yii\helpers\VarDumper::dumpAsString($invoice->getErrors()));
        } else {
            return $invoice;
        }
    }
}
