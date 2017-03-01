<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\components\validators\lesson\conflict\HolidayValidator;
use common\components\validators\lesson\conflict\TeacherValidator;
use common\components\validators\lesson\conflict\StudentValidator;
use common\components\validators\lesson\conflict\IntraEnrolledLessonValidator;

/**
 * This is the model class for table "lesson".
 *
 * @property string $id
 * @property string $enrolmentId
 * @property string $teacherId
 * @property string $date
 * @property int $status
 * @property int $isDeleted
 */
class Lesson extends \yii\db\ActiveRecord
{
    const TYPE_PRIVATE_LESSON = 1;
    const TYPE_GROUP_LESSON = 2;
    const STATUS_DRAFTED = 1;
    const STATUS_SCHEDULED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;
	const STATUS_UNSCHEDULED = 5;
	const STATUS_MISSED = 6;
	
    const SCENARIO_REVIEW = 'review';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_EDIT_REVIEW_LESSON = 'edit-review-lesson';
    const SCENARIO_CREATE = 'create';

    public $programId;
    public $time;
    public $hours;
    public $program_name;
	public $showAllReviewLessons = false;
	public $present;
	public $toEmailAddress;
	public $subject;
	public $content;
	public $vacationId;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson';
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
            [['courseId', 'teacherId', 'status', 'isDeleted', 'duration'], 'required'],
            [['courseId', 'status'], 'integer'],
            [['date', 'programId','colorCode', 'classroomId'], 'safe'],
			
			[['date'], HolidayValidator::className(), 'on' => self::SCENARIO_CREATE],
			[['date'], TeacherValidator::className(), 'on' => self::SCENARIO_CREATE],
			[['date'], StudentValidator::className(), 'on' => self::SCENARIO_CREATE],
            [['programId','date'], 'required', 'on' => self::SCENARIO_CREATE],
			
            ['date', TeacherValidator::className(), 'on' => self::SCENARIO_EDIT_REVIEW_LESSON],
            ['date', StudentValidator::className(), 'on' => self::SCENARIO_EDIT_REVIEW_LESSON],
            ['date', HolidayValidator::className(), 'on' => self::SCENARIO_EDIT_REVIEW_LESSON],
			
            [['date'], TeacherValidator::className(), 'on' => self::SCENARIO_REVIEW],
            [['date'], StudentValidator::className(), 'on' => self::SCENARIO_REVIEW, 'when' => function($model, $attribute) {
				return $model->course->program->isPrivate();
			}],
            [['date'], HolidayValidator::className(), 'on' => self::SCENARIO_REVIEW],
            [['date'], IntraEnrolledLessonValidator::className(), 'on' => self::SCENARIO_REVIEW],
			
            ['date', HolidayValidator::className(), 'on' => self::SCENARIO_EDIT],
            ['date', TeacherValidator::className(), 'on' => self::SCENARIO_EDIT],
			['date', StudentValidator::className(), 'on' => self::SCENARIO_EDIT, 'when' => function($model, $attribute) {
				return $model->course->program->isPrivate();
			}],
            ['teacherId', TeacherValidator::className(), 'on' => self::SCENARIO_EDIT],
			
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Lesson ID',
            'programId' => 'Program Name',
            'courseId' => 'Course ID',
            'teacherId' => 'Teacher Name',
            'date' => 'Date',
            'status' => 'Status',
            'isDeleted' => 'Is Deleted',
            'time' => 'From Time',
            'toTime' => 'To time',
            'colorCode' => 'Color Code',
			'classroomId' => 'Classroom',
			'SummariseReport' => 'Summaries Only'
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\LessonQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\LessonQuery(get_called_class());
    }

	public function isScheduled()
	{
		return (int) $this->status === self::STATUS_SCHEDULED;
	}
	
	public function isUnscheduled()
	{
		return (int) $this->status === self::STATUS_UNSCHEDULED;
	}

	public function isCompleted()
	{
		return (int) $this->status === self::STATUS_COMPLETED;
	}

	public function isMissed()
	{
		return (int) $this->status === self::STATUS_MISSED;
	}

	public function isCanceled()
	{
		return (int) $this->status === self::STATUS_CANCELED;
	}

	public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['courseId' => 'courseId']);
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'courseId']);
    }

    public function getPrivateLesson()
    {
        return $this->hasOne(PrivateLesson::className(), ['lessonId' => 'id']);
    }
	
	public function getClassroom()
    {
        return $this->hasOne(Classroom::className(), ['id' => 'classroomId']);
    }

    public function getPaymentCycle()
    {
        return $this->hasOne(PaymentCycle::className(), ['id' => 'paymentCycleId'])
                    ->viaTable('payment_cycle_lesson', ['lessonId' => 'id']);
    }

    public function getPaymentCycleLesson()
    {
        return $this->hasOne(PaymentCycleLesson::className(), ['lessonId' => 'id']);
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->viaTable('invoice_line_item', ['item_id' => 'id'])
            ->onCondition(['invoice.type' => Invoice::TYPE_INVOICE, 'invoice.isDeleted' => false]);
    }

    public function getProFormaInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
                ->via('proFormaLineItem')
                ->andWhere(['invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE,
                    'invoice.isDeleted' => false]);
    }

    public function getLessonReschedule()
    {
        return $this->hasOne(LessonReschedule::className(), ['lessonId' => 'id']);
    }

    public function getInvoiceLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['item_id' => 'id'])
                ->where(['invoice_line_item.item_type_id' => ItemType::TYPE_PRIVATE_LESSON]);
    }

    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacherId']);
    }

	public function getProFormaLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['item_id' => 'id'])
			->via('paymentCycleLesson')
            ->andWhere(['item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON]);
    }
	
    public function getStatus()
    {
        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
        $currentDate = new \DateTime();
        $status = null;
        switch ($this->status) {
            case self::STATUS_SCHEDULED:
                if ($lessonDate >= $currentDate) {
                $status = 'Scheduled';
                } else {
                    $status = 'Completed';
                }
            break;
            case self::STATUS_COMPLETED:
                $status = 'Completed';
				if ($lessonDate <= $currentDate) {
                	$status = 'Completed';
				}
            break;
            case self::STATUS_CANCELED:
                $status = 'Canceled';
            break;
			case self::STATUS_UNSCHEDULED:
                $status = 'Unscheduled';
            break;
			case self::STATUS_MISSED:
				if ($lessonDate >= $currentDate) {
					$status = 'Scheduled';
				} else {
					$status = 'Completed';
				}
            break;
        }

        return $status;
    }

    public static function lessonStatuses()
    {
        return [
            self::STATUS_COMPLETED => Yii::t('common', 'Completed'),
            self::STATUS_SCHEDULED => Yii::t('common', 'Scheduled'),
        ];
    }

    public function getColorCode()
    {
        if (!empty($this->colorCode)) {
            $colorCode = $this->colorCode;
        } else if ($this->isRescheduled()) {
            $defaultRescheduledLessonEventColor = CalendarEventColor::findOne(['cssClass' => 'lesson-rescheduled']);
            $colorCode = $defaultRescheduledLessonEventColor->code;
        } else {
            $defaultLessonEventColor = CalendarEventColor::findOne(['cssClass' => 'private-lesson']);
            $colorCode = $defaultLessonEventColor->code;
        }

        return $colorCode;
    }

    public function isPrivate()
    {
        return ((int) $this->course->program->type === (int) Program::TYPE_PRIVATE_PROGRAM);
    }

    public function beforeSave($insert)
    {
		if (isset($this->colorCode)) {
            if ($this->isRescheduled()) {
                $defaultRescheduledLessonEventColor = CalendarEventColor::findOne(['cssClass' => 'lesson-rescheduled']);
                if ($this->colorCode === $defaultRescheduledLessonEventColor->code) {
                    $this->colorCode = null;
                }
            } else if ($this->isPrivate()) {
                $defaultPrivateLessonEventColor = CalendarEventColor::findOne(['cssClass' => 'private-lesson']);
                if ($this->colorCode === $defaultPrivateLessonEventColor->code) {
                    $this->colorCode = null;
                }
            }
        }
        if ($insert) {
            $this->classroomId = $this->getTeacherClassroomId();
        }

        return parent::beforeSave($insert);
    }

	public function afterSave($insert, $changedAttributes)
    {
        if ((int) $this->status !== (int) self::STATUS_DRAFTED) {
            if (!$insert) {
                if ((isset($changedAttributes['date']) && !empty($this->date)) || isset($changedAttributes['teacherId'])) {                
					$teacherId = $this->teacherId;
					if(isset($changedAttributes['date']) && !empty($this->date)) {
						$fromDate = \DateTime::createFromFormat('Y-m-d H:i:s', $changedAttributes['date']);
	                    $toDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
						
						$this->updateAttributes([
							'date' => $fromDate->format('Y-m-d H:i:s'),
							'status' => self::STATUS_CANCELED,
                    	]);
					} else {
						$this->updateAttributes([
							'status' => self::STATUS_CANCELED,
							'teacherId' => $changedAttributes['teacherId']
                    	]);	
					}
                    $originalLessonId = $this->id;
                    $this->id = null;
                    $this->isNewRecord = true;
					if(isset($changedAttributes['date']) && !empty($this->date)) {
                    	$this->date = $toDate->format('Y-m-d H:i:s');
					}
					if(isset($changedAttributes['teacherId'])) {
                    	$this->teacherId = $teacherId;
					}
                    $this->status = self::STATUS_SCHEDULED;
                    $this->save();
                    $lessonRescheduleModel = new LessonReschedule();
                    $lessonRescheduleModel->lessonId = $originalLessonId;
                    $lessonRescheduleModel->rescheduledLessonId = $this->id;
                    $lessonRescheduleModel->save();
                }
            }

            return parent::afterSave($insert, $changedAttributes);
        }
    }

    public function isFirstLessonDate($paymentCycleStartDate, $paymentCycleEndDate)
    {
        $priorDate       = (new \DateTime())->modify('+15 day');
        $priorDate       = new \DateTime($priorDate->format('Y-m-d'));
        $lesson          = Lesson::find()
            ->where(['courseId' => $this->courseId])
            ->unInvoicedProForma()
            ->scheduled()
            ->between($paymentCycleStartDate, $paymentCycleEndDate)
            ->orderBy(['lesson.date' => SORT_ASC])
            ->one();
        $lessonStartDate = \DateTime::createFromFormat('Y-m-d H:i:s',
                    $lesson->date);
        $lessonStartDate = new \DateTime($lessonStartDate->format('Y-m-d'));
        return $lessonStartDate == $priorDate;
    }

	public function getRootLessonId($lessonId)
    {
        $parent = (new Query())->select(['lessonId'])
            ->from('lesson_reschedule')
            ->where(['rescheduledLessonId' => $lessonId])
            ->scalar();

        if (!empty($parent)) {
            return $this->getRootLessonId($parent);
        }
        return $lessonId;
    }

    public function getRootLesson()
    {
        $rootLessonId = $this->getRootLessonId($this->id);
        return self::findOne(['id' => $rootLessonId]);
    }

    public function isRescheduled()
    {
        $rootLessonId = $this->getRootLessonId($this->id);

        return $rootLessonId !== $this->id;
    }

	public function getDuration()
    {
        $duration		 = \DateTime::createFromFormat('H:i:s', $this->duration);
		$hours			 = $duration->format('H');
		$minutes		 = $duration->format('i');
		$lessonDuration	 = $hours + ($minutes / 60);
		
		return $lessonDuration;
    }

	public function getGroupLessonCount()
    {
        $courseCount  = Lesson::find()
			->andWhere(['courseId' => $this->courseId])
			->count('id');
		
		return $courseCount;
    }

    public function isEnrolmentFirstlesson()
    {
        $courseId             = $this->courseId;
        $enrolmentFirstLesson = self::find()
                ->where(['courseId' => $courseId])
                ->andWhere(['status' =>[self::STATUS_SCHEDULED, self::STATUS_COMPLETED]])
                ->orderBy(['date' => SORT_ASC])
                ->one();
        return $enrolmentFirstLesson->date === $this->date;
    }

    public function getTeacherClassroomId()
    {
        $classroomId         = null;
        $teacherLocationId   = $this->teacher->userLocation->id;
        $day                 = (new \DateTime($this->date))->format('N');
        $start               = new \DateTime($this->date);
        $duration            = new \DateTime($this->duration);
        $end                 = $start->add(new \DateInterval('PT'.$duration->format('H').'H'.$duration->format('i').'M'));
        $teacherAvailability = TeacherAvailability::find()
            ->andWhere(['day' => $day, 'teacher_location_id' => $teacherLocationId])
            ->andWhere(['AND',
                ['<=', 'from_time', $start->format('H:i:s')],
                ['>=', 'to_time', $end->format('H:i:s')]
            ])
            ->one();

        if (!empty($teacherAvailability->teacherRoom)) {
            $classroomId = $teacherAvailability->teacherRoom->classroomId;
            $unavailability = ClassroomUnavailability::find()
                    ->andWhere(['classroomId' => $classroomId])
                    ->andWhere(['AND',
                        ['<=', 'DATE(fromDate)', $start->format('Y-m-d')],
                        ['>=', 'DATE(toDate)', $start->format('Y-m-d')]
                    ])
                    ->one();
            if (!empty($unavailability)) {
                $classroomId = null;
            }
        }
        return $classroomId;
    }

	public function sendEmail()
    {
        $subject                      = $this->subject;
		return Yii::$app->mailer->compose('lesson-reschedule',
			[
				'toName' => $this->enrolment->student->customer->publicIdentity,
				'content' => $this->content,
			])
			->setFrom(\Yii::$app->params['robotEmail'])
			->setTo($this->toEmailAddress)
			->setSubject($subject)
			->send();
	}

    public function createInvoice()
    {
        $location_id = Yii::$app->session->get('location_id');
        $invoice = new Invoice();
        $invoice->user_id = $this->enrolment->student->customer->id;
        $invoice->location_id = $location_id;
        $invoice->type = INVOICE::TYPE_INVOICE;
        $invoice->save();
        $invoice->addLineItem($this);
        $invoice->save();
        if ($this->hasProFormaInvoice()) {
            $netPrice = yii::$app->formatter->asDecimal($this->proFormaLineItem->netPrice, 2);
            if ($this->proFormaInvoice->proFormaCredit >= $netPrice) {
                $invoice->addPayment($this->proFormaInvoice);
            }
        }

        return $invoice;
    }

    public function hasProFormaInvoice()
    {
        return !empty($this->proFormaInvoice);
    }

    public function hasInvoice()
    {
        return !empty($this->invoice);
    }

    public function addPaymentCycleLesson()
    {
        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
        $paymentCycle = PaymentCycle::find()
            ->where(['enrolmentId' => $this->enrolment->id])
            ->andWhere(['AND', ['<=', 'startDate', $lessonDate->format('Y-m-d')],
                ['>=', 'endDate', $lessonDate->format('Y-m-d')]
            ])
            ->one();
        $paymentCycleLesson                 = new PaymentCycleLesson();
        $paymentCycleLesson->paymentCycleId = $paymentCycle->id;
        $paymentCycleLesson->lessonId       = $this->id;
        $paymentCycleLesson->save();
    }
}
