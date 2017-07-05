<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\components\validators\lesson\conflict\HolidayValidator;
use common\components\validators\lesson\conflict\ClassroomValidator;
use common\components\validators\lesson\conflict\TeacherValidator;
use common\components\validators\lesson\conflict\StudentValidator;
use common\components\validators\lesson\conflict\IntraEnrolledLessonValidator;
use common\components\validators\lesson\conflict\TeacherAvailabilityValidator;
use common\components\validators\lesson\conflict\StudentAvailabilityValidator;

/**
 * This is the model class for table "lesson".
 *
 * @property string $id
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
    const DEFAULT_MERGE_DURATION = '00:15:00';
    const DEFAULT_LESSON_DURATION = '00:15:00';
    const DEFAULT_EXPLODE_DURATION_SEC = 900;

	const MAXIMUM_LIMIT = 48;
    const TYPE_REGULAR = 1;
    const TYPE_EXTRA = 2;

    const SCENARIO_MERGE = 'merge';
    const SCENARIO_REVIEW = 'review';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_EDIT_REVIEW_LESSON = 'edit-review-lesson';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_SPLIT = 'split';
    const SCENARIO_GROUP_ENROLMENT_REVIEW = 'group-enrolment';
    const SCENARIO_EDIT_CLASSROOM = 'classroom-edit';
    
    const TEACHER_VIEW = 1;
    const CLASS_ROOM_VIEW = 2;

    const EVENT_RESCHEDULE_ATTEMPTED	 = 'RescheduleAttempted';
    const EVENT_RESCHEDULED			 = 'Rescheduled';
    const EVENT_UNSCHEDULE_ATTEMPTED	 = 'UnscheduleAttempted';
    const EVENT_UNSCHEDULED			 = 'Unscheduled';
    const EVENT_MISSED = 'missed';

	const APPLY_SINGLE_LESSON = 1;
	const APPLY_ALL_FUTURE_LESSONS = 2;
    public $enrolmentId;
    public $studentFullName;
    public $programId;
    public $time;
    public $hours;
    public $program_name;
    public $showAllReviewLessons = false;
    public $present;
    public $toEmailAddress;
    public $subject;
    public $content;
    public $newDuration;
    public $vacationId;
    public $studentId;
    public $userName;
    public $applyContext;
    public $locationId;

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
            [['teacherId', 'status', 'duration'], 'required'],
            ['courseId', 'required', 'when' => function($model, $attribute) {
                    return $model->type !== self::TYPE_EXTRA;
            }],
            [['courseId', 'status', 'type'], 'integer'],
            [['date', 'programId','colorCode', 'classroomId', 'isDeleted', 'applyContext'], 'safe'],
            [['classroomId'], ClassroomValidator::className(), 'on' => self::SCENARIO_EDIT_CLASSROOM],
            [['date'], HolidayValidator::className(), 'on' => [self::SCENARIO_CREATE, self::SCENARIO_MERGE]],
            [['date'], StudentValidator::className(), 'on' => [self::SCENARIO_CREATE, self::SCENARIO_MERGE]],
            [['programId','date'], 'required', 'on' => self::SCENARIO_CREATE],
			
            ['date', TeacherValidator::className(), 'on' => [self::SCENARIO_EDIT_REVIEW_LESSON, 
                self::SCENARIO_MERGE]],
            ['date', StudentValidator::className(), 'on' => self::SCENARIO_EDIT_REVIEW_LESSON],
            ['date', HolidayValidator::className(), 'on' => self::SCENARIO_EDIT_REVIEW_LESSON],
			
            [['date'], TeacherValidator::className(), 'on' => self::SCENARIO_REVIEW],
            [['date'], StudentValidator::className(), 'on' => self::SCENARIO_REVIEW, 'when' => function($model, $attribute) {
				return $model->course->program->isPrivate();
			}],
            [['date'], HolidayValidator::className(), 'on' => self::SCENARIO_REVIEW],
            [['date'], IntraEnrolledLessonValidator::className(), 'on' => [self::SCENARIO_REVIEW, self::SCENARIO_MERGE]],
            ['date', HolidayValidator::className(), 'on' => self::SCENARIO_EDIT],
            ['date', TeacherValidator::className(), 'on' => self::SCENARIO_EDIT],
			['date', StudentValidator::className(), 'on' => self::SCENARIO_EDIT, 'when' => function($model, $attribute) {
				return $model->course->program->isPrivate();
			}],
            ['teacherId', TeacherValidator::className(), 'on' => self::SCENARIO_EDIT],
            ['date', StudentValidator::className(), 'on' => self::SCENARIO_GROUP_ENROLMENT_REVIEW],
			
            ['duration', TeacherAvailabilityValidator::className(), 'on' => self::SCENARIO_SPLIT],
            ['duration', StudentAvailabilityValidator::className(), 'on' => self::SCENARIO_SPLIT],

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
			'summariseReport' => 'Summarize Results',
			'toEmailAddress' => 'To'
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

    public function isExploded()
    {
        return !empty($this->lessonSplit);
    }

    public function isCompleted()
    {
        $lessonDate  = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
        $currentDate = new \DateTime();
        return $lessonDate <= $currentDate;
    }

    public function isMissed()
    {
        return (int) $this->status === self::STATUS_MISSED;
    }

    public function isCanceled()
    {
        return (int) $this->status === self::STATUS_CANCELED;
    }

    public function getFullDuration()
    {
        $duration = $this->duration;
            foreach ($this->extendedLessons as $extendedLesson) {
                $additionalDuration = new \DateTime($extendedLesson->lessonSplit->unit);
                $lessonDuration = new \DateTime($duration);
                $lessonDuration->add(new \DateInterval('PT' . $additionalDuration->format('H')
                    . 'H' . $additionalDuration->format('i') . 'M'));
                $duration = $lessonDuration->format('H:i:s');
            }
        return $duration;
    }

    public function isDeletable()
    {
        return !$this->isDeleted;
    }

    public function canExplode()
    {
        return $this->isPrivate() && $this->isUnscheduled() && !$this->isExploded()
            && !$this->privateLesson->isExpired();
    }

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['courseId' => 'courseId']);
    }

    public function getEnrolmentDiscount()
    {
        return $this->hasOne(EnrolmentDiscount::className(), ['enrolmentId' => 'id'])
			->via('enrolment');
    }

    public function getLessonSplit()
    {
        return $this->hasOne(LessonSplit::className(), ['lessonId' => 'id']);
    }

    public function getExtendedLessons()
    {
        return $this->hasMany(LessonSplitUsage::className(), ['extendedLessonId' => 'id']);
    }

	public function getLessonSplitUsage()
    {
        return $this->hasOne(LessonSplitUsage::className(), ['lessonSplitId' => 'id'])
			->via('lessonSplit');
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
                    ->via('paymentCycleLesson');
    }

    public function getPaymentCycleLesson()
    {
        if ($this->isSplitRescheduled()) {
            return $this->hasOne(PaymentCycleLesson::className(), ['lessonId' => 'lessonId'])
                ->via('reschedule');
        } else {
            return $this->hasOne(PaymentCycleLesson::className(), ['lessonId' => 'id']);
        }
    }

    public function getLessonSplits()
    {
        if ($this->isRescheduled()) {
            return $this->hasMany(LessonSplit::className(), ['lessonId' => 'lessonId'])
                ->via('reschedule');
        } else {
            return $this->hasMany(LessonSplit::className(), ['lessonId' => 'id']);
        }
    }

    public function getLessonSplitsUsage()
    {
        return $this->hasMany(LessonSplitUsage::className(), ['lessonId' => 'id'])
            ->via('lessonSplitsUsage');
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->via('invoiceLineItems')
                ->onCondition(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE]);
    }

    public function getInvoiceItemPaymentCycleLessons()
    {
        return $this->hasMany(InvoiceItemPaymentCycleLesson::className(), ['paymentCycleLessonId' => 'id'])
            ->via('paymentCycleLesson');
    }

    public function getInvoiceItemPaymentCycleLessonSplits()
    {
        return $this->hasMany(InvoiceItemPaymentCycleLessonSplit::className(), ['lessonSplitId' => 'id'])
            ->via('lessonSplits');
    }

    public function getInvoiceLineItems()
    {
        if (!$this->isGroup()) {
            return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
                ->via('invoiceItemLessons')
                    ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_PRIVATE_LESSON]);
        } else {
            return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
                ->via('invoiceItemsEnrolment')
                    ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_GROUP_LESSON]);
        }
    }

    public function getInvoiceItemsEnrolment()
    {
        return $this->hasMany(InvoiceItemEnrolment::className(), ['enrolmentId' => 'enrolmentId'])
            ->via('course');
    }

    public function getInvoiceItemLessons()
    {
        return $this->hasMany(InvoiceItemLesson::className(), ['lessonId' => 'id']);
    }

    public function getProFormaLineItems()
    {
        if ($this->isExploded() || $this->isSplitRescheduled()) {
            return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
                ->via('invoiceItemPaymentCycleLessonSplits')
                    ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_LESSON_SPLIT]);
        } else if (!$this->isExtra()) {
            return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
                ->via('invoiceItemPaymentCycleLessons')
                    ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON]);
        } else {
            return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
                ->via('invoiceItemLessons')
                    ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_EXTRA_LESSON]);
        }
    }

    public function getProFormaInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->via('proFormaLineItems')
                ->onCondition(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
    }

    public function getLessonReschedule()
    {
        return $this->hasOne(LessonReschedule::className(), ['lessonId' => 'id']);
    }
    
    public function getEnrolments()
    {
        return $this->hasMany(Enrolment::className(), ['courseId' => 'courseId'])
            ->onCondition(['enrolment.isDeleted' => false, 'enrolment.isConfirmed' => true]);;
    }

    public function hasGroupInvoice()
    {
        foreach ($this->enrolments as $enrolment) {
            if (!$enrolment->hasInvoice($this->id)) {
                return false;
            }
        }
        return !empty($this->enrolments);
        }

    public function isSplitRescheduled()
    {
        return !empty($this->reschedule) ? $this->reschedule->lesson->isExploded() : false;
    }

    public function getReschedule()
    {
        return $this->hasOne(LessonReschedule::className(), ['rescheduledLessonId' => 'id']);
    }

    public function getInvoiceLineItem()
    {
        $lessonId = $this->id;
        if ($this->hasInvoice()) {
        return InvoiceLineItem::find()
            ->where(['invoice_id' => $this->invoice->id])
            ->joinWith(['lineItemLesson' => function ($query) use ($lessonId) {
                $query->where(['lessonId' => $lessonId]);
            }])
            ->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_PRIVATE_LESSON])
            ->one();
        } else {
            return null;
        }
    }

    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacherId']);
    }

    public function getScheduleTitle()
    {
        if ($this->isGroup()) {
            return $this->course->program->name;
        } else {
            return $this->enrolment->student->fullName;
        }
    }

    public function getClassroomTitle()
    {
        return $this->enrolment->student->fullName;
    }

    public function getClass()
    {
        if (!empty($this->colorCode)) {
            $class = null;
        } else if ($this->isMissed()) {
            $class = 'lesson-missed';
        } else if($this->isEnrolmentFirstlesson()) {
            $class = 'first-lesson';
        } else if ($this->isPrivate()) {
            $class = 'private-lesson';
        } else if ($this->isGroup()) {
            $class = 'group-lesson';
        }
        if ($this->getRootLesson()) {
            $rootLesson = $this->getRootLesson();
            if($rootLesson->id !== $this->id) {
                $class = 'lesson-rescheduled';
            }
            if ($rootLesson->teacherId !== $this->teacherId) {
                $class = 'teacher-substituted';
            }
        }

        return $class;
    }

    public function getProFormaLineItem()
    {
        $lessonId = $this->id;
        if (!$this->isSplitRescheduled() && !$this->isExploded() && !$this->isExtra()) {
            $paymentCycleLessonId = $this->paymentCycleLesson->id;
        }
        if ($this->isSplitRescheduled()) {
            $lessonId = $this->reschedule->lesson->id;
        }
        if ($this->hasProFormaInvoice()) {
            if ($this->isExtra()) {
                return InvoiceLineItem::find()
                    ->andWhere(['invoice_id' => $this->proFormaInvoice->id])
                    ->joinWith(['lineItemLesson' => function ($query) use ($lessonId) {
                        $query->where(['lessonId' => $lessonId]);
                    }])
                    ->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_EXTRA_LESSON])
                    ->one();
            } else if ($this->isExploded() || $this->isSplitRescheduled()) {
                return InvoiceLineItem::find()
                    ->andWhere(['invoice_id' => $this->proFormaInvoice->id])
                    ->joinWith(['lineItemPaymentCycleLessonSplit' => function ($query) use ($lessonId) {
                        $query->joinWith(['lessonSplit' => function ($query) use ($lessonId) {
                            $query->where(['lessonId' => $lessonId]);
                        }]);
                    }])
                    ->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_LESSON_SPLIT])
                    ->one();
            } else {
                return InvoiceLineItem::find()
                    ->andWhere(['invoice_id' => $this->proFormaInvoice->id])
                    ->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON])
                    ->joinWith(['lineItemPaymentCycleLesson' => function ($query) use ($paymentCycleLessonId) {
                        $query->where(['paymentCycleLessonId' => $paymentCycleLessonId]);
                    }])
                    ->one();
            }
        } else {
            return null;
        }
    }

    public function getStatus()
    {
        $status = null;
        switch ($this->status) {
            case self::STATUS_SCHEDULED:
                if (!$this->isCompleted()) {
                $status = 'Scheduled';
                } else {
                    $status = 'Completed';
                }
            break;
            case self::STATUS_COMPLETED:
                $status = 'Completed';
                if ($this->isCompleted()) {
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
                if (!$this->isCompleted()) {
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
        } else {
            $defaultColor = CalendarEventColor::findOne(['cssClass' => $this->getClass()]);
            $colorCode = $defaultColor->code;
        }

        return $colorCode;
    }

    public function isPrivate()
    {
        return (int) $this->course->program->type === (int) Program::TYPE_PRIVATE_PROGRAM;
    }

    public function isGroup()
    {
        return (int) $this->course->program->type === Program::TYPE_GROUP_PROGRAM;
    }

    public function isExtra()
    {
        return ((int) $this->type === (int) self::TYPE_EXTRA);
    }

    public function isExpired()
    {
        $currentDate = new \DateTime();
        $expiryDate  = new \DateTime($this->privateLesson->expiryDate);
        return $currentDate > $expiryDate;
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
			$this->isDeleted = false;
            if(empty($this->type)) {
                $this->type = Lesson::TYPE_REGULAR;
            }
            $this->classroomId = $this->getTeacherClassroomId();
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$this->isDraftLesson()) {
            if (!$insert) {
                if ($this->isRescheduledLesson($changedAttributes)) {
                    $this->trigger(self::EVENT_RESCHEDULE_ATTEMPTED);
                }
                if($this->isRescheduledByClassroom($changedAttributes)) {
                    $this->trigger(self::EVENT_RESCHEDULED);
                }
				if($this->isUnscheduled()) {
					$privateLessonModel = new PrivateLesson();
					$privateLessonModel->lessonId = $this->id;
					$date = new \DateTime($this->date);
					$expiryDate = $date->modify('90 days');
					$privateLessonModel->expiryDate = $expiryDate->format('Y-m-d H:i:s');
					$privateLessonModel->save();
				}
            }
			
        }
		
        return parent::afterSave($insert, $changedAttributes);
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

    public function canMerge()
    {
        $lessonDuration = new \DateTime($this->duration);
        $date = new \DateTime($this->date);
        $date->add(new \DateInterval('PT' . $lessonDuration->format('H') . 'H' . $lessonDuration->format('i') . 'M'));
        $lesson = new Lesson();
        $lesson->setScenario(self::SCENARIO_MERGE);
        $lesson->date = $date->format('Y-m-d H:i:s');
        $lesson->duration = self::DEFAULT_MERGE_DURATION;
        $lesson->teacherId = $this->teacherId;
        $lesson->courseId = $this->courseId;
        $lesson->status = self::STATUS_SCHEDULED;
        $lesson->isDeleted = false;

        return $lesson->validate() && $this->enrolment->hasExplodedLesson()
            && !$this->isExploded();
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

    public function isRescheduledByDate($changedAttributes)
    {
        return isset($changedAttributes['date']) &&
            !empty($this->date) && new \DateTime($changedAttributes['date'])
                !== new \DateTime($this->date);
    }

    public function isRescheduledByClassroom($changedAttributes)
    {
        return empty($changedAttributes['classroomId']) ||
            (!empty($changedAttributes['classroomId']) && (int)$changedAttributes['classroomId']
                !== (int)$this->classroomId);
    }

    public function isRescheduledByTeacher($changedAttributes)
    {
        return isset($changedAttributes['teacherId']) &&
            (int)$changedAttributes['teacherId'] !== (int)$this->teacherId;
    }

    public function isDraftLesson()
    {
        return (int) $this->status === (int) self::STATUS_DRAFTED;
    }

    public function isRescheduledLesson($changedAttributes)
    {
        return $this->isRescheduledByDate($changedAttributes) ||
            $this->isRescheduledByTeacher($changedAttributes);
    }

    public function getDuration()
    {
        $duration		 = \DateTime::createFromFormat('H:i:s', $this->duration);
        $hours			 = $duration->format('H');
        $minutes		 = $duration->format('i');
        $lessonDuration	 = $hours + ($minutes / 60);

        return $lessonDuration;
    }

    public function getDurationSec()
    {
        $duration		 = \DateTime::createFromFormat('H:i:s', $this->duration);
        $hours			 = $duration->format('H');
        $minutes		 = $duration->format('i');
        $lessonDuration	 = (($hours) * 60) * 60 + ($minutes * 60);

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
                        ->notDeleted()
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
        if(!empty($this->toEmailAddress)) {
            $content = [];
            foreach($this->toEmailAddress as $email) {
                $subject                      = $this->subject;
                $content[] = Yii::$app->mailer->compose('lesson-reschedule', [
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

    public function createInvoice()
    {
        $invoice = new Invoice();
        $invoice->on(Invoice::EVENT_CREATE, [new InvoiceLog(), 'create']);
        $invoice->type = INVOICE::TYPE_INVOICE;
        $invoice->createdUserId = Yii::$app->user->id;
        $invoice->updatedUserId = Yii::$app->user->id;
        return $invoice;
    }

    public function createPrivateLessonInvoice()
    {
        $invoice = $this->createInvoice();
        $location_id = $this->enrolment->student->customer->userLocation->location_id;
        $user = User::findOne(['id' => $this->enrolment->student->customer]);
        $invoice->userName = $user->publicIdentity;
        $invoice->user_id = $this->enrolment->student->customer->id;
        $invoice->location_id = $location_id;
        $invoice->save();
        $invoice->addPrivateLessonLineItem($this);
        $invoice->save();
        if ($this->hasProFormaInvoice()) {
            if ($this->isSplitRescheduled()) {
                $netPrice = $this->getSplitRescheduledAmount();
            } else {
                $netPrice = $this->proFormaLineItem->netPrice;
            }
            if ($this->proFormaInvoice->proFormaCredit >= $netPrice) {
                $invoice->addPayment($this->proFormaInvoice, $netPrice);
            } else {
                $invoice->addPayment($this->proFormaInvoice, $this->proFormaInvoice->proFormaCredit);
            }
        }
        if (!empty($this->extendedLessons)) {
            foreach ($this->extendedLessons as $extendedLesson) {
                $invoice->lineItem->addLessonCreditApplied($extendedLesson->lessonSplitId);
            }
        }

        return $invoice;
    }

    public function getUnit()
    {
        $getDuration = \DateTime::createFromFormat('H:i:s', $this->duration);
        $hours       = $getDuration->format('H');
        $minutes     = $getDuration->format('i');
        return (($hours * 60) + $minutes) / 60;
    }

    public function createGroupInvoice($enrolmentId)
    {
        $invoice   = $this->createInvoice();
        $enrolment = Enrolment::findOne($enrolmentId);
        $courseCount = $enrolment->courseCount;
        $location_id = $enrolment->student->customer->userLocation->location_id;
        $user = User::findOne(['id' => $enrolment->student->customer]);
        $invoice->userName = $user->publicIdentity;
        $invoice->user_id = $enrolment->student->customer->id;
        $invoice->location_id = $location_id;
        $invoice->save();
        $this->enrolmentId = $enrolmentId;
        $invoice->addGroupLessonLineItem($this);
        $invoice->save();
        if ($enrolment->hasProFormaInvoice()) {
            $netPrice = $enrolment->proFormaInvoice->netSubtotal / $courseCount;
            if ($enrolment->proFormaInvoice->proFormaCredit >= $netPrice) {
                $invoice->addPayment($enrolment->proFormaInvoice, $netPrice);
            } else {
                $invoice->addPayment($enrolment->proFormaInvoice, $enrolment->proFormaInvoice->proFormaCredit);
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

	public function getPresent()
	{
		$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
		$currentDate = new \DateTime();
		if($lessonDate > $currentDate) {
			$result = '-';
		}
		if($lessonDate < $currentDate) {
			$result = 'Yes';
		}
		if($this->isMissed()) {
			$result = 'No';
		} 
		return $result;
	}
	public function getCreditUsage()
    {
		$duration = $this->duration;
	    $lessonCreditUsage = LessonSplit::find()
		   ->select(['SEC_TO_TIME( SUM( TIME_TO_SEC(unit))) as unit'])
		   ->innerJoinWith('lessonSplitUsage')
		   ->andWhere(['lessonId' => $this->id])
		   ->one();
		if(!empty($lessonCreditUsage->unit)) {
			$originalCredits = new \DateTime($this->duration);
			$usedCredits = new \DateTime($lessonCreditUsage->unit);
			$difference = $originalCredits->diff($usedCredits );
			$duration = $difference ->format('%H:%I');;
		}
		return $duration;
	}
	public function isHoliday()
    {
		$startDate = (new \DateTime($this->course->startDate))->format('Y-m-d');
       	$holidays = Holiday::find()
			->andWhere(['>=', 'DATE(date)', $startDate])
            ->all();
		$holidayDates = ArrayHelper::getColumn($holidays, function ($element) {
    		return (new \DateTime($element->date))->format('Y-m-d');
		});
		$lessonDate = (new \DateTime($this->date))->format('Y-m-d');
		return in_array($lessonDate, $holidayDates);
}

    public function getSplitRescheduledAmount()
    {
        $getDuration   = new \DateTime($this->reschedule->lesson->getCreditUsage());
        $hours         = $getDuration->format('H');
        $minutes       = $getDuration->format('i');
        $unit          = (($hours * 60) + $minutes) / 60;
        $amount        = $this->enrolment->program->rate * $unit;
        $discount      = $this->proFormaLineItem->discount;
        $discountType  = $this->proFormaLineItem->discountType;
        if ((int) $discountType === (int) InvoiceLineItem::DISCOUNT_FLAT) {
            $discountValue = $discount;
        } else {
            $discountValue = ($discount / 100) * $amount;
        }

        return $amount - $discountValue;
    }

    public function canInvoice()
    {
        return $this->isCompleted() && $this->isScheduled();
    }

    public function createExtraLessonCourse()
    {
        $course = new Course();
        $course->programId   = $this->programId;
        $course->teacherId   = $this->teacherId;
        $course->startDate   = $this->date;
        $course->isConfirmed = true;
        $course->locationId  = $this->locationId;
        $course->save();
        return $course;
    }
}
