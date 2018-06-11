<?php

namespace common\models;

use Yii;
use common\models\discount\EnrolmentDiscount;
use yii\helpers\ArrayHelper;
use yii\behaviors\BlameableBehavior;
use valentinek\behaviors\ClosureTable;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\components\validators\lesson\conflict\HolidayValidator;
use common\components\validators\lesson\conflict\ClassroomValidator;
use common\components\validators\lesson\conflict\TeacherEligibleValidator;
use common\components\validators\lesson\conflict\TeacherLessonOverlapValidator;
use common\components\validators\lesson\conflict\StudentValidator;
use common\components\validators\lesson\conflict\IntraEnrolledLessonValidator;
use common\components\validators\lesson\conflict\TeacherSubstituteValidator;

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
    use Payable;
    use Invoiceable;
    
    const TYPE_PRIVATE_LESSON = 1;
    const TYPE_GROUP_LESSON = 2;
    
    const STATUS_RESCHEDULED = 1;
    const STATUS_SCHEDULED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_UNSCHEDULED = 5;
    const STATUS_EXPIRED = 10;
    const DEFAULT_MERGE_DURATION = '00:15:00';
    const DEFAULT_LESSON_DURATION = '00:15:00';
    const DEFAULT_EXPLODE_DURATION_SEC = 900;
    const STATUS_PRESENT='Yes';
    const STATUS_ABSENT='No';

    const MAXIMUM_LIMIT = 48;
    const TYPE_REGULAR = 1;
    const TYPE_EXTRA = 2;

    const SCENARIO_CREATE_GROUP = 'group-extra-lesson-create';
    const SCENARIO_SUBSTITUTE_TEACHER = 'substitute-teacher';
    const SCENARIO_LESSON_EDIT_ON_SCHEDULE = 'lesson-edit-schedule';
    const SCENARIO_MERGE = 'merge';
    const SCENARIO_REVIEW = 'review';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_EDIT_REVIEW_LESSON = 'edit-review-lesson';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_SPLIT = 'split';
    const SCENARIO_GROUP_ENROLMENT_REVIEW = 'group-enrolment';
    const SCENARIO_EDIT_CLASSROOM = 'classroom-edit';
    const TEACHER_UNSCHEDULED_ERROR_MESSAGE = '<p style="background-color: yellow">Warning: Teacher Unscheduled</p>';

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
    public $newDuration;
    public $vacationId;
    public $studentId;
    public $userName;
    public $applyContext;
    public $locationId;
    public $programRate;
    public $applyFullDiscount;

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
            [
                'class' => ClosureTable::className(),
                'tableName' => 'lesson_hierarchy',
                'childAttribute' => 'childLessonId',
                'parentAttribute' => 'lessonId',
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId'
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
            ['courseId', 'required', 'when' => function ($model, $attribute) {
                return $model->type !== self::TYPE_EXTRA;
            }],
            [['courseId', 'status', 'type'], 'integer'],
            ['programRate', 'required', 'on' => self::SCENARIO_CREATE_GROUP],
            [['date', 'programId','colorCode', 'classroomId', 'isDeleted', 'applyFullDiscount',
                'isExploded', 'applyContext', 'isConfirmed', 'createdByUserId', 'updatedByUserId', 'isPresent'], 'safe'],
            [['classroomId'], ClassroomValidator::className(),
                'on' => [self::SCENARIO_EDIT_CLASSROOM]],
            [['date'], HolidayValidator::className(),
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_MERGE, self::SCENARIO_CREATE_GROUP,
                self::SCENARIO_REVIEW, self::SCENARIO_EDIT, self::SCENARIO_EDIT_REVIEW_LESSON]],
            [['date'], StudentValidator::className(), 'on' => [self::SCENARIO_CREATE, self::SCENARIO_MERGE,
                self::SCENARIO_GROUP_ENROLMENT_REVIEW]],
            [['programId','date', 'duration'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_CREATE_GROUP]],
            ['date', TeacherEligibleValidator::className(), 'on' => [
                self::SCENARIO_EDIT_REVIEW_LESSON, self::SCENARIO_EDIT,
                self::SCENARIO_MERGE, self::SCENARIO_REVIEW, self::SCENARIO_LESSON_EDIT_ON_SCHEDULE]],
            ['date', TeacherLessonOverlapValidator::className(), 'on' => [
                self::SCENARIO_EDIT_REVIEW_LESSON, self::SCENARIO_EDIT, self::SCENARIO_CREATE_GROUP,
                self::SCENARIO_MERGE, self::SCENARIO_REVIEW, self::SCENARIO_LESSON_EDIT_ON_SCHEDULE]],
            [['date'], StudentValidator::className(), 'on' => [
                self::SCENARIO_EDIT_REVIEW_LESSON,
                self::SCENARIO_REVIEW, self::SCENARIO_EDIT], 'when' => function ($model, $attribute) {
                    return $model->course->program->isPrivate();
                }],
            ['date', 'validateOnInvoiced', 'on' => self::SCENARIO_EDIT],
            [['date'], TeacherSubstituteValidator::className(), 'on' => self::SCENARIO_SUBSTITUTE_TEACHER],
            [['date'], IntraEnrolledLessonValidator::className(), 'on' => [self::SCENARIO_REVIEW, self::SCENARIO_MERGE]]
        ];
    }

    public function validateOnInvoiced($attribute)
    {
        if ($this->hasInvoice()) {
            $this->addError($attribute, "Lesson can't be edited once its invoiced");
        }
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
            'toEmailAddress' => 'To',
            'showAllReviewLessons' => 'Show All',
            'isPresent' => 'Present'
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
    
    public function isScheduledOrRescheduled()
    {
        return (int) $this->status === self::STATUS_SCHEDULED || 
                (int) $this->status === self::STATUS_RESCHEDULED;
    }
    
    public function isResolveSingleLesson()
    {
        return (int) $this->applyContext === self::APPLY_SINGLE_LESSON;
    }
    public function isUnscheduled()
    {
        return (int) $this->status === self::STATUS_UNSCHEDULED;
    }
    
    public function isCompleted()
    {
        $lessonDate  = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
        $currentDate = new \DateTime();
        return $lessonDate <= $currentDate;
    }
    
    public function isCanceled()
    {
        return (int) $this->status === self::STATUS_CANCELED;
    }

    public function getLeafs()
    {
        $leafIds = [];
        $childrens = self::find()->childrenOf($this->id)->all();
        $lessons = null;
        if ($childrens) {
            $leafIds = $this->getNestedLeafs($childrens, $leafIds);
            $lessons = self::find()->where(['id' => $leafIds])->all();
        }
        return $lessons;
    }

    public function getNestedLeafs($childrens, $leafIds)
    {
        foreach ($childrens as $parent) {
            $children = self::find()->childrenOf($parent->id)->all();
            if (!$children) {
                $leafIds[] = $parent->id;
            } else {
                $leafIds = $this->getNestedLeafs($children, $leafIds);
            }
        }
        return $leafIds;
    }
    
    public function cancel()
    {
        $this->status = self::STATUS_CANCELED;
        
        return $this->save();
    }

    public function hasExpiryDate()
    {
        return !empty($this->privateLesson);
    }
    
    public function getFullDuration()
    {
        $duration = $this->duration;
        foreach ($this->usedLessonSplits as $extendedLesson) {
            $additionalDuration = new \DateTime($extendedLesson->lesson->duration);
            $lessonDuration = new \DateTime($duration);
            $lessonDuration->add(new \DateInterval('PT' . $additionalDuration->format('H')
                    . 'H' . $additionalDuration->format('i') . 'M'));
            $duration = $lessonDuration->format('H:i:s');
        }
        return $duration;
    }

    public function isDeletable()
    {
        return !$this->isDeleted && !$this->hasInvoice() && $this->isPrivate();
    }

    public function getLastHierarchy()
    {
        return $this->hasOne(LessonHierarchy::className(), ['lessonId' => 'id'])->orderBy(['depth' => SORT_DESC]);
    }

    public function canExplode()
    {
        return $this->isPrivate() && $this->isUnscheduled() && !$this->isExploded
            && !$this->isExpired() && !$this->isExtra() && !$this->hasInvoice();
    }

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['courseId' => 'courseId']);
    }
    
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentId'])
                ->via('enrolment');
    }
    
    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customer_id'])
                ->via('student');
    }

    public function getEnrolmentDiscount()
    {
        return $this->hasOne(EnrolmentDiscount::className(), ['enrolmentId' => 'id'])
            ->via('enrolment');
    }

    public function getExtendedLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'extendedLessonId'])
            ->via('lessonSplitUsage');
    }

    public function isExtendedLesson()
    {
        return !empty($this->usedLessonSplits);
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'courseId']);
    }

	public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'programId'])
			->via('course');
    }

    public function getPrivateLesson()
    {
        return $this->hasOne(PrivateLesson::className(), ['lessonId' => 'id']);
    }
    
    public function getCourseProgramRate()
    {
        return $this->hasOne(CourseProgramRate::className(), ['courseId' => 'courseId']);
    }

    public function getClassroom()
    {
        return $this->hasOne(Classroom::className(), ['id' => 'classroomId']);
    }

    public function isOwing($enrolmentId)
    {
        return $this->getCreditAppliedAmount($enrolmentId) < $this->amount;
    }

    public function getOwingAmount($enrolmentId)
    {
        return $this->amount - $this->getCreditAppliedAmount($enrolmentId);
    }

    public function getPaymentCycle()
    {
        return $this->hasOne(PaymentCycle::className(), ['id' => 'paymentCycleId'])
                    ->via('paymentCycleLesson')
                    ->onCondition(['payment_cycle.isDeleted' => false]);
    }

    public function getLessonPayments()
    {
        return $this->hasMany(LessonPayment::className(), ['lessonId' => 'id']);
    }

    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['id' => 'paymentId'])
            ->viaTable('lesson_payment', ['lessonId' => 'id'])
            ->onCondition(['payment.isDeleted' => false]);
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'paymentId'])
            ->viaTable('lesson_payment', ['lessonId' => 'id'])
            ->onCondition(['payment.isDeleted' => false]);
    }

    public function getPaymentCycleLesson()
    {
        return $this->hasOne(PaymentCycleLesson::className(), ['lessonId' => 'id'])
            ->onCondition(['payment_cycle_lesson.isDeleted' => false]);
    }

    public function getLessonSplitUsage()
    {
        return $this->hasOne(LessonSplitUsage::className(), ['lessonId' => 'id']);
    }

    public function getUsedLessonSplits()
    {
        return $this->hasMany(LessonSplitUsage::className(), ['extendedLessonId' => 'id']);
    }

    public function getBulkRescheduleLesson()
    {
        return $this->hasOne(BulkRescheduleLesson::className(), ['lessonId' => 'id']);
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

    public function getInvoiceLineItems()
    {
        if (!$this->isGroup()) {
            return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
                ->via('invoiceItemLessons')
                    ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_PRIVATE_LESSON,
                            'invoice_line_item.isDeleted' => false]);
        } else {
            return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
                ->via('invoiceItemsEnrolment')
                    ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_GROUP_LESSON,
                        'invoice_line_item.isDeleted' => false]);
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
        return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
                ->via('invoiceItemPaymentCycleLessons')
                    ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON,
                        'invoice_line_item.isDeleted' => false]);
    }
   
    public function getRootLesson()
    {
        return self::find()->ancestorsOf($this->id)->orderBy(['id' => SORT_ASC])->one();
    }

    public function hasRootLesson()
    {
        return !empty($this->rootLesson);
    }
 
    public function getProFormaInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->via('proFormaLineItems')
                ->onCondition(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
    }

    public function getGroupProFormaLineItem($enrolment)
    {
        $lessonId = $this->id;
        $enrolmentId = $enrolment->id;
        return InvoiceLineItem::find()
                ->notDeleted()
                ->joinWith(['lineItemLesson' => function ($query) use ($lessonId) {
                    $query->andWhere(['lessonId' => $lessonId]);
                }])
                ->joinWith(['lineItemEnrolment' => function ($query) use ($enrolmentId) {
                    $query->andWhere(['invoice_item_enrolment.enrolmentId' => $enrolmentId]);
                }])
                ->one();
    }
    
    public function hasGroupProFormaLineItem($enrolment)
    {
        return !empty($this->getGroupProFormaLineItem($enrolment));
    }

    public function getLessonReschedule()
    {
        return $this->hasOne(LessonHierarchy::className(), ['lessonId' => 'id'])
                ->onCondition(['lesson_hierarchy.depth' => true]);
    }
    
    public function getEnrolments()
    {
        return $this->hasMany(Enrolment::className(), ['courseId' => 'courseId'])
            ->onCondition(['enrolment.isDeleted' => false, 'enrolment.isConfirmed' => true]);
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

    public function getTeacherCost()
    {
        $qualification = Qualification::find()
            ->andWhere(['teacher_id' => $this->teacherId, 'program_id' => $this->course->programId])
            ->one();
        return !empty($qualification->rate) ? $qualification->rate : 0.00;
    }

    public function hasMerged()
    {
        return !empty($this->lessonSplitUsage);
    }

    public function getReschedule()
    {
        return $this->hasOne(LessonHierarchy::className(), ['childLessonId' => 'id']);
    }
    
    public function getInvoiceLineItem()
    {
        $lessonId = $this->id;
        if ($this->hasInvoice()) {
            return InvoiceLineItem::find()
                ->notDeleted()
            ->andWhere(['invoice_id' => $this->invoice->id])
            ->joinWith(['lineItemLesson' => function ($query) use ($lessonId) {
                $query->andWhere(['lessonId' => $lessonId]);
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
    
    public function getTeacherProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'teacherId']);
    }

    public function getLessonNumber()
    {
        $number = str_pad($this->id, 6, 0, STR_PAD_LEFT);
        return 'L-' . $number;
    }
    public function getScheduleTitle()
    {
        if ($this->isGroup()) {
            return $this->course->program->name;
        } else {
            return $this->enrolment->student->fullName;
        }
    }
    public function getClass()
    {
        if (!empty($this->colorCode)) {
            $class = null;
        } elseif (!$this->isExtra () && $this->isEnrolmentFirstlesson()) {
            $class = 'first-lesson';
        } elseif ($this->isPrivate()) {
            $class = 'private-lesson';
        } elseif ($this->isGroup()) {
            $class = 'group-lesson';
        }
        if ($this->rootLesson && empty($this->colorCode)) {
            if ($this->isRescheduled()) {
                $class = 'lesson-rescheduled';
            } elseif ($this->rootLesson->teacherId !== $this->teacherId) {
                $class = 'teacher-substituted';
            }
        }

        return $class;
    }

    public function getProFormaLineItem()
    {
        if ($this->hasProFormaInvoice()) {
            $paymentCycleLessonId = $this->paymentCycleLesson->id;
            return InvoiceLineItem::find()
                    ->notDeleted()
                    ->andWhere(['invoice_id' => $this->proFormaInvoice->id])
                    ->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON])
                    ->joinWith(['lineItemPaymentCycleLessons' => function ($query) use ($paymentCycleLessonId) {
                        $query->andWhere(['paymentCycleLessonId' => $paymentCycleLessonId]);
                    }])
                    ->one();
        } else {
            return null;
        }
    }

    public function getStatus()
    {
        $status = null;
	 if ($this->isExpired()) {
		$status = 'Expired';
		}
		switch ($this->status) {
            case self::STATUS_SCHEDULED:
                if (!$this->isCompleted()) {
                    $status = 'Scheduled';
                } else {
                    $status = 'Completed';
                }
            break;
            case self::STATUS_CANCELED:
                $status = 'Canceled';
            break;
            case self::STATUS_RESCHEDULED:
                if (!$this->isCompleted()) {
                    $status = 'Rescheduled';
                } else {
                    $status = 'Completed';
                }
            break;
            case self::STATUS_UNSCHEDULED:
		 if (!$this->isExpired()) {
                $status = 'Unscheduled';	    
                if ($this->isExploded) {
                    $status .= ' (Exploded)';
                } 
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
        if ($this->privateLesson) {
            $expiryDate  = new \DateTime($this->privateLesson->expiryDate);
        }
        return !empty($this->privateLesson) ? $currentDate > $expiryDate : false;
    }

    public function beforeSave($insert)
    {
        if (isset($this->colorCode)) {
            if ($this->isRescheduled()) {
                $defaultRescheduledLessonEventColor = CalendarEventColor::findOne(['cssClass' => 'lesson-rescheduled']);
                if ($this->colorCode === $defaultRescheduledLessonEventColor->code) {
                    $this->colorCode = null;
                }
            } elseif ($this->isPrivate()) {
                $defaultPrivateLessonEventColor = CalendarEventColor::findOne(['cssClass' => 'private-lesson']);
                if ($this->colorCode === $defaultPrivateLessonEventColor->code) {
                    $this->colorCode = null;
                }
            } elseif ($this->isGroup()) {
                $defaultGroupLessonEventColor = CalendarEventColor::findOne(['cssClass' => 'group-lesson']);
                if ($this->colorCode === $defaultGroupLessonEventColor->code) {
                    $this->colorCode = null;
                }
            }
        }
        if ($insert) {
            $this->isDeleted = false;
            $this->isPresent = true;
            if (empty($this->isExploded)) {
                $this->isExploded = false;
            }
            if (empty($this->type)) {
                $this->type = Lesson::TYPE_REGULAR;
            }
            $this->classroomId = $this->getTeacherClassroomId();
        }

        return parent::beforeSave($insert);
    }
    
    public function afterSoftDelete()
    {
        if ($this->isPrivate() && $this->proFormaLineItem) {
            if (!$this->hasCreditApplied($this->enrolment->id)) {
                $this->proFormaLineItem->delete();
            } else if (!$this->hasCreditUsed($this->enrolment->id)) {
                $invoice = $this->addLessonCreditInvoice();
                $payment = new Payment();
                $payment->amount = $this->getLessonCreditAmount($this->enrolment->id);
                $invoice->addPayment($this, $payment, $this->enrolment);
            }
        }
        return true;
    }

    public function getAmount()
    {
        return $this->courseProgramRate->programRate * $this->unit;
    }

    public function getAvailabilities()
    {
        return $this->hasMany(TeacherAvailability::className(), ['teacher_location_id' => 'id'])
          ->viaTable('user_location', ['user_id' => 'teacherId']);
    }

    public function checkAsReschedule()
    {
        return $this->isConfirmed && $this->isScheduled() && $this->rootLesson && !$this->bulkRescheduleLesson;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            if ($this->isCanceled()) {
                $this->trigger(self::EVENT_RESCHEDULE_ATTEMPTED);
            }
            if ($this->checkAsReschedule()) {
                if (new \DateTime($this->rootLesson->date) != new \DateTime($this->date)) {
                    $this->updateAttributes(['status' => self::STATUS_RESCHEDULED]);
                }
            }
            $options = [
                'cluster' => env('PUSHER_CLUSTER'),
                'encrypted' => true
            ];
            $pusher = new \Pusher\Pusher(
                env('PUSHER_KEY'),
                env('PUSHER_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );
            if(!isset($changedAttributes['isConfirmed']) && $this->isConfirmed) {
                $pusher->trigger('lesson', 'lesson-edit', '');
            }
        }
        
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function canMerge()
    {
        if ($this->enrolment->hasExplodedLesson() && !$this->isExploded && !$this->isExtra() && !$this->hasInvoice() && !$this->isCanceled() && !$this->isUnscheduled()) {
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

            return $lesson->validate();
        }
        return false;
    }

    public function isRescheduled()
    {
        return (int) $this->status === self::STATUS_RESCHEDULED;
    }
    public function hasSubstituteByTeacher() {
	    if(!empty($this->rootLesson)) {
	    return  $this->rootLesson->teacherId !== $this->teacherId;
	    }
	    return false;
    }

    public function isRescheduledByDate($changedAttributes)
    {
        return isset($changedAttributes['date']) &&
            !empty($this->date) && new \DateTime($changedAttributes['date'])
                != new \DateTime($this->date);
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
                ->isConfirmed()
                ->count('id');

        return $courseCount;
    }

    public function isEnrolmentFirstlesson()
    {
        $courseId             = $this->courseId;
        $enrolmentFirstLesson = self::find()
                        ->notDeleted()
            ->andWhere(['courseId' => $courseId])
            ->notCanceled()
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

    public function getUnit()
    {
        if ($this->usedLessonSplits) {
            $unit = $this->fullDuration;
        } else {
            $unit = $this->duration;
        }
        $getDuration = \DateTime::createFromFormat('H:i:s', $unit);
        $hours       = $getDuration->format('H');
        $minutes     = $getDuration->format('i');
        return (($hours * 60) + $minutes) / 60;
    }

    public function getLessonCreditAmount($enrolmentId)
    {
        return Payment::find()
                ->joinWith('lessonCredit')
                ->andWhere(['lessonId' => $this->id, 'enrolmentId' => $enrolmentId])
                ->notDeleted()
                ->sum('amount');
    }
    
    public function getCreditAppliedAmount($enrolmentId)
    {
        return Payment::find()
                ->joinWith('lessonCredit')
                ->andWhere(['lessonId' => $this->id, 'enrolmentId' => $enrolmentId])
                ->creditApplied()
                ->notDeleted()
                ->sum('amount');
    }

    public function getCreditAppliedPayment($enrolmentId)
    {
        return Payment::find()
                ->joinWith('lessonCredit')
                ->andWhere(['lessonId' => $this->id, 'enrolmentId' => $enrolmentId])
                ->creditApplied()
                ->notDeleted()
                ->all();
    }
    
    public function getCreditUsedAmount($enrolmentId)
    {
        return Payment::find()
                ->joinWith('lessonCredit')
                ->andWhere(['lessonId' => $this->id, 'enrolmentId' => $enrolmentId])
                ->creditUsed()
                ->notDeleted()
                ->sum('amount');
    }

    public function getCreditUsedPayment($enrolmentId)
    {
        return Payment::find()
                ->joinWith('lessonCredit')
                ->andWhere(['lesson_payment.lessonId' => $this->id, 'lesson_payment.enrolmentId' => $enrolmentId])
                ->creditUsed()
                ->notDeleted()
                ->all();
    }
    public function getProformaLessonItem()
    {
        return $this->hasOne(ProformaItemLesson::className(), ['lessonId' => 'id']);
    }

    public function hasCreditUsed($enrolmentId)
    {
        return !empty($this->getCreditUsedPayment($enrolmentId));
    }

    public function hasCreditApplied($enrolmentId)
    {
        return !empty($this->getCreditAppliedPayment($enrolmentId));
    }

    public function hasPaymentCycleLesson()
    {
        return !empty($this->paymentCycleLesson);
    }
    
    public function hasLessonCredit($enrolmentId)
    {
        return $this->getLessonCreditAmount($enrolmentId) > 0.01;
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
        return $this->isPresent ? 'Yes' : 'No';
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

    public function getSplitedAmount()
    {
        $rootLesson = $this->rootLesson;
        if (!$rootLesson->proFormaLineItem) {
            echo $rootLesson->id;die;
        }
        return $rootLesson->proFormaLineItem->itemTotal / ($rootLesson->durationSec / self::DEFAULT_EXPLODE_DURATION_SEC);
    }

    public function canInvoice()
    {
        return ($this->isCompleted() && $this->isScheduledOrRescheduled()) || $this->isExpired() || (!$this->isPresent);
    }

    public function getLastChild()
    {
        return $this->children()->orderBy(['lesson.id' => SORT_DESC])->one();
    }

    public function unschedule()
    {
        $this->status = self::STATUS_UNSCHEDULED;
        $this->save();
        return true;
    }
    
    public function takePayment()
    {
        if (!$this->hasProFormaInvoice()) {
            if (!$this->paymentCycle->hasProFormaInvoice()) {
                $this->paymentCycle->createProFormaInvoice();
            } else {
                $lineItem = $this->addPrivateLessonLineItem($this->paymentCycle->proFormaInvoice);
                $lineItem->invoice->save();
            }
        } else {
            $this->proFormaInvoice->makeInvoicePayment($this);
        }
        
        return $this->paymentCycle->proFormaInvoice;
    }
    
    public static function instantiate($row)
    {
        switch ($row['type']) {
            case ExtraLesson::TYPE:
                return new ExtraLesson();
            default:
               return new self;
        }
    }
    
    public function makeAsRoot()
    {
        if ($this->markAsRoot()) {
            return $this->setExpiry();
        }
    }
    
    public function makeAsChild($lesson)
    {
        if ($this->append($lesson)) {
            return $lesson->setExpiry();
        }
    }

    public function setExpiry()
    {
        if (!$this->privateLesson && $this->isPrivate()) {
            if ($this->rootLesson) {
                $expiryDate = new \DateTime($this->rootLesson->privateLesson->expiryDate);
                $date       = new \DateTime($this->date);
                if ($date >= $expiryDate) {
                    $expiryDate = $date->modify('1 day');
                }
            } else {
                $date = new \DateTime($this->date);
                $expiryDate = $date->modify('90 days');
            }
            $privateLessonModel = new PrivateLesson();
            $privateLessonModel->lessonId = $this->id;
            $privateLessonModel->expiryDate = $expiryDate->format('Y-m-d H:i:s');
            $privateLessonModel->save();
            return $privateLessonModel;
        }
    }
    
    public function rescheduleTo($lesson)
    {
        $lessonRescheduleModel = new LessonReschedule();
        $lessonRescheduleModel->lessonId = $this->id;
        $lessonRescheduleModel->rescheduledLessonId = $lesson->id;
        return $lessonRescheduleModel->save();
    }
    public function getLeaf()
    {
        return self::find()->descendantsOf($this->id)->orderBy(['id' => SORT_DESC])->one();
    }
    public function dailyScheduleStatus() {
	$status = $this->getStatus();
	    if($this->status === self::STATUS_CANCELED) {
		$status = "Rescheduled to " . Yii::$app->formatter->asDate($this->leaf->date);    
	    }    
	return $status; 
    }
}
