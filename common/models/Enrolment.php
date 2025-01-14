<?php

namespace common\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\models\discount\EnrolmentDiscount;
use DateInterval;
use common\models\discount\LessonDiscount;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;
use yii\data\ArrayDataProvider;
use Carbon\Carbon;
use common\components\queue\EnrolmentDiscount as EnrolmentDiscountQueue;

/**
 * This is the model class for table "enrolment".
 *
 * @property string $id
 * @property string $courseId
 * @property string $studentId
 * @property int $isDeleted
 * @property bool is_online
 */
class Enrolment extends \yii\db\ActiveRecord
{
    use Invoiceable;

    public $studentIds;
    public $endDate;
    public $toEmailAddress;
    public $subject;
    public $content;
    public $hasEditable;
    public $programName;
    public $enrolmentCount;
    public $userName;
    public $applyFullDiscount;
    public $scheduleTitle;
    public $class;
    public $lessonCount;

    const AUTO_RENEWAL_DAYS_FROM_END_DATE = 90;
    const AUTO_RENEWAL_STATE_ENABLED = 'enabled';
    const AUTO_RENEWAL_STATE_DISABLED = 'disabled';
    const LESSONS_COUNT = 96;
    const TYPE_REGULAR = 1;
    const TYPE_EXTRA = 2;
    const TYPE_REVERSE = 'reverse';
    const ENROLMENT_EXPIRY = 90;
    const EVENT_CREATE = 'create';
    const EVENT_GROUP = 'group-course-enroll';
    const CONSOLE_USER_ID  = 727;

    const SCENARIO_EDIT = 'scenario-edit';
    const SCENARIO_GROUP_ENROLMENT_ENDDATE_ADJUSTMENT = 'scenario-group-enrolment-enddate-adjustment';
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
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
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
            [['courseId'], 'required'],
            [['courseId', 'studentId'], 'integer'],
            [[
                'paymentFrequencyId', 'isDeleted', 'isConfirmed', 'createdAt',
                'hasEditable', 'isAutoRenew', 'is_online', 'applyFullDiscount', 'updatedAt', 'createdByUserId',
                'updatedByUserId', 'endDateTime', 'isEnableInfo','scheduleTitle', 'class'
            ], 'safe'],
            ['courseId', 'validateOnEdit', 'on' => self::SCENARIO_EDIT],
            ['endDateTime', 'validateOnAdjustment', 'on' => self::SCENARIO_GROUP_ENROLMENT_ENDDATE_ADJUSTMENT]
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
            'showAllEnrolments' => 'Show All',
            'showActiveFutureEnrolments' => 'Show Active Future Enrolments',
            'isAutoRenew' => 'Auto Renew',
            'is_online' => 'Online'
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
        return $this->hasOne(Course::className(), ['id' => 'courseId'])
            ->onCondition(['course.isDeleted' => false]);
    }
    public function getCourseSchedules()
    {
        return $this->hasMany(CourseSchedule::className(), ['courseId' => 'id'])
            ->via('course');
    }
    public function getCourseSchedule()
    {
        return $this->hasOne(CourseSchedule::className(), ['courseId' => 'id'])
            ->orderBy(['course_schedule.id' => SORT_DESC])
            ->via('course');
    }
    public function getPaymentsFrequency()
    {
        return $this->hasOne(PaymentFrequency::className(), ['id' => 'paymentFrequencyId']);
    }

    public function getPaymentFrequencyDiscount()
    {
        return $this->hasOne(EnrolmentDiscount::className(), ['enrolmentId' => 'id'])
            ->onCondition(['type' => EnrolmentDiscount::TYPE_PAYMENT_FREQUENCY]);
    }

    public function getGroupDiscount()
    {
        return $this->hasOne(EnrolmentDiscount::className(), ['enrolmentId' => 'id'])
            ->onCondition(['type' => EnrolmentDiscount::TYPE_GROUP]);
    }

    public function getMultipleEnrolmentDiscount()
    {
        return $this->hasOne(EnrolmentDiscount::className(), ['enrolmentId' => 'id'])
            ->onCondition(['type' => EnrolmentDiscount::TYPE_MULTIPLE_ENROLMENT]);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentId']);
    }

    public function getGroupStudents()
    {
        return $this->hasMany(Student::className(), ['id' => 'studentId']);
    }

    public function getCurrentPaymentCycleDateRange($date)
    {
        $currentPaymentCycle = $this->getCurrentPaymentcycle($date);
        if (!$currentPaymentCycle) {
            $startDate = new \DateTime($date);
            $endDate = new \DateTime($date);
        } else {
            $startDate = new \DateTime($currentPaymentCycle->startDate);
            $endDate = new \DateTime($currentPaymentCycle->endDate);
        }
        return $startDate->format('Y-m-d') . ' - ' . $endDate->format('Y-m-d');
    }

    public function validateOnEdit($attribute)
    {
        if ($this->course->isPrivate()) {
            if (!$this->hasPartialyPaidPaymentCycle()) {
                $this->addError($attribute, "You can't edit discounts.");
            }
        } else {
            if ($this->hasPaidLesson()) {
                $this->addError($attribute, "You can't edit discounts.");
            }
        }
    }

    public function validateOnAdjustment($attribute)
    {
        if ($this->course->program->isGroup()) {
            $enrolment = Enrolment::findOne($this->id);
            if (Carbon::parse($this->endDateTime) > Carbon::parse($enrolment->endDateTime)) {
                $this->addError($attribute, "You can't extend group enrolments");
            }
        }
    }

    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customer_id'])
            ->via('student');
    }

    public function getFirstUnpaidLesson()
    {
        foreach ($this->lessonsByDate as $lesson) {
            $payment = LessonPayment::find()
                ->notDeleted()
                ->andWhere(['enrolmentId' => $this->id, 'lessonId' => $lesson->id])
                ->one();
            if (!$payment) {
                return $lesson;
            }
        }
        return null;
    }

    public function hasUnpaidLesson()
    {
        $status = false;
        foreach ($this->lessonsByDate as $lesson) {
            $payment = LessonPayment::find()
                ->notDeleted()
                ->andWhere(['enrolmentId' => $this->id, 'lessonId' => $lesson->id])
                ->one();
            if (!$payment) {
                $status = true;
                break;
            }
        }
        return $status;
    }

    public function hasPaidLesson()
    {
        $status = false;
        foreach ($this->lessonsByDate as $lesson) {
            $payment = LessonPayment::find()
                ->notDeleted()
                ->andWhere(['enrolmentId' => $this->id, 'lessonId' => $lesson->id])
                ->one();
            if ($payment) {
                $status = true;
                break;
            }
        }
        return $status;
    }

    public function getPaymentCycles()
    {
        return $this->hasMany(PaymentCycle::className(), ['enrolmentId' => 'id'])
            ->orderBy(['payment_cycle.startDate' => SORT_ASC])
            ->onCondition(['payment_cycle.isDeleted' => false]);
    }

    public function getCourseProgramRate()
    {
        return $this->hasOne(CourseProgramRate::className(), ['courseId' => 'courseId']);
    }

    public function getCourseProgramRates()
    {
        return $this->hasMany(CourseProgramRate::className(), ['courseId' => 'courseId']);
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

    public function getPrivateLessonProFormaInvoices()
    {
        return $this->hasMany(Invoice::className(), ['id' => 'invoice_id'])
            ->via('privateLessonLineItems')
            ->onCondition(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
    }

    public function getPaymentCycleLessons()
    {
        return $this->hasMany(PaymentCycleLesson::className(), ['lessonId' => 'id'])
            ->via('lessons')
            ->onCondition(['payment_cycle_lesson.isDeleted' => false]);
    }

    public function getLineItemPaymentCycleLessons()
    {
        return $this->hasMany(InvoiceItemPaymentCycleLesson::className(), ['paymentCycleLessonId' => 'id'])
            ->via('paymentCycleLessons');
    }

    public function getProFormaInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->via('invoiceLineItems')
            ->onCondition(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
    }

    public function getProFormaInvoices()
    {
        return $this->hasMany(Invoice::className(), ['id' => 'invoice_id'])
            ->via('invoiceLineItems')
            ->onCondition(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
    }

    public function hasInvoice($lessonId)
    {
        return !empty($this->getInvoice($lessonId));
    }

    public function canDeleted()
    {
        $completedLessons = Lesson::find()
            ->notDeleted()
            ->joinWith(['course' => function ($query) {
                $query->joinWith(['enrolment' => function ($query) {
                    $query->andWhere(['enrolment.id' => $this->id]);
                }])
                    ->notDeleted()
                    ->confirmed();
            }])
            ->isConfirmed()
            ->andWhere(['<=', 'date', (new \DateTime())->format('Y-m-d H:i:s')])
            ->exists();
        return empty($completedLessons) ? true : false;
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
            ->onCondition([
                'invoice_line_item.item_type_id' => ItemType::TYPE_GROUP_LESSON,
                'invoice_line_item.isDeleted' => false
            ]);
    }

    public function getPrivateLessonLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
            ->via('lineItemPaymentCycleLessons')
            ->onCondition([
                'invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON,
                'invoice_line_item.isDeleted' => false
            ]);
    }

    public function getInvoiceItemsEnrolment()
    {
        return $this->hasMany(InvoiceItemEnrolment::className(), ['enrolmentId' => 'id']);
    }

    public function getCurrentPaymentCycle($date)
    {
        $currentPaymentCycle = PaymentCycle::find()
            ->andWhere(['enrolmentId' => $this->id])
            ->notDeleted()
            ->andWhere([
                'AND',
                ['<=', 'startDate', (new \DateTime($date))->format('Y-m-d')],
                ['>=', 'endDate', (new \DateTime($date))->format('Y-m-d')]
            ])
            ->one();
        return $currentPaymentCycle;
    }

    public function getNextPaymentCycle()
    {
        $currentPaymentCycleEndDate = new \DateTime($this->currentPaymentCycle->endDate);
        return $this->hasOne(PaymentCycle::className(), ['enrolmentId' => 'id'])
            ->andWhere(['>', 'startDate', $currentPaymentCycleEndDate->format('Y-m-d')])
            ->onCondition(['payment_cycle.isDeleted' => false]);
    }

    public function getFirstLesson()
    {
        return $this->hasOne(Lesson::className(), ['courseId' => 'courseId'])
            ->onCondition(['lesson.isDeleted' => false, 'lesson.isConfirmed' => true])
            ->orderBy(['date' => SORT_ASC]);
    }

    public function getFirst()
    {
        return $this->hasOne(Lesson::className(), ['courseId' => 'courseId'])
            ->onCondition(['lesson.isDeleted' => false, 'lesson.isConfirmed' => true])
            ->onCondition(['!=', 'lesson.status', Lesson::STATUS_CANCELED ])
            ->orderBy(['date' => SORT_ASC]);
    }

    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'courseId'])
            ->onCondition([
                'lesson.isDeleted' => false, 'lesson.isConfirmed' => true,
                'lesson.status' => [
                    Lesson::STATUS_RESCHEDULED, Lesson::STATUS_SCHEDULED,
                    Lesson::STATUS_UNSCHEDULED
                ]
            ]);
    }

    public function getFutureLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'courseId'])
            ->onCondition([
                'lesson.isDeleted' => false, 'lesson.isConfirmed' => true,
                'lesson.status' => [
                    Lesson::STATUS_RESCHEDULED, Lesson::STATUS_SCHEDULED,
                    Lesson::STATUS_UNSCHEDULED
                ]
            ])
            ->andWhere('DATE(lesson.date)>= CURRENT_DATE')
            ->andWhere('DATE(lesson.date)<= DATE(\''.$this->endDateTime.'\')')
            ->orderBy(['lesson.date' => SORT_ASC]);
    }

    public function getLessonsByDate()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'courseId'])
            ->onCondition([
                'lesson.isDeleted' => false, 'lesson.isConfirmed' => true,
                'lesson.status' => [
                    Lesson::STATUS_RESCHEDULED, Lesson::STATUS_SCHEDULED,
                    Lesson::STATUS_UNSCHEDULED
                ]
            ])
            ->orderBy(['lesson.date' => SORT_ASC]);
    }

    public function getLastLesson()
    {
        return $this->hasOne(Lesson::className(), ['courseId' => 'courseId'])
            ->onCondition([
                'lesson.isDeleted' => false, 'lesson.isConfirmed' => true,
                'lesson.status' => [
                    Lesson::STATUS_RESCHEDULED, Lesson::STATUS_SCHEDULED,
                    Lesson::STATUS_UNSCHEDULED
                ]
            ])
            ->orderBy(['lesson.date' => SORT_DESC]);
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
            ->isConfirmed()
            ->notDeleted()
            ->notCanceled()
            ->andWhere(['courseId' => $this->courseId])
            ->count('id');
    }

    public function getFirstUnPaidPaymentCycle()
    {
        foreach ($this->paymentCycles as $paymentCycle) {
            if (!$paymentCycle->isFullyPaid() && !$paymentCycle->hasInvoicedLesson()) {
                return $paymentCycle;
            }
        }

        return null;
    }

    public function getPartialyPaidPaymentCycle()
    {
        foreach ($this->paymentCycles as $paymentCycle) {
            if ((!$paymentCycle->isFullyPaid() || $paymentCycle->hasPartialyPaidLesson()) && !$paymentCycle->hasInvoicedLesson()) {
                return $paymentCycle;
            }
        }
        return null;
    }

    public function hasPartialyPaidPaymentCycle()
    {
        return !empty($this->partialyPaidPaymentCycle);
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

    public function getUnPaidPaymentCycles()
    {
        $models = [];
        foreach ($this->paymentCycles as $paymentCycle) {
            if (!$paymentCycle->isFullyPaid()) {
                $models[] = $paymentCycle;
            }
        }

        return $models;
    }

    public function getGroupDiscountValue()
    {
        return $this->groupDiscount ? $this->groupDiscount->discountType == EnrolmentDiscount::VALUE_TYPE_DOLLAR ? '$' . $this->groupDiscount->discount :
            $this->groupDiscount->discount . '%' : 'Not set';
    }

    public function getlastPaymentCycle()
    {
        return $this->hasOne(PaymentCycle::className(), ['enrolmentId' => 'id'])
            ->orderBy(['endDate' => SORT_DESC])
            ->onCondition(['payment_cycle.isDeleted' => false]);
    }

    public function isMonthlyPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) PaymentFrequency::LENGTH_MONTHLY;
    }

    public function isQuaterlyPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) PaymentFrequency::LENGTH_QUARTERLY;
    }

    public function isHalfYearlyPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) PaymentFrequency::LENGTH_HALFYEARLY;
    }

    public function isAnnualPaymentFrequency()
    {
        return (int) $this->paymentFrequency === (int) PaymentFrequency::LENGTH_FULL;
    }

    public function isExpiring($daysCount)
    {
        $isExpiring = false;
        $endDate = (new \DateTime($this->course->endDate))->format('Y-m-d');
        $currentDate = new \DateTime();
        $currentDate = $currentDate->modify('+' . $daysCount . ' days');
        $expiryDate = $currentDate->format('Y-m-d');
        if ($endDate <= $expiryDate) {
            $isExpiring = true;
        }
        return $isExpiring;
    }

    public function isActive()
    {
        $isActive = true;
        $startDate = (new \DateTime($this->course->startDate))->format('Y-m-d');
        $endDate = (new \DateTime($this->course->endDate))->format('Y-m-d');
        $currentDate = (new \DateTime())->format('Y-m-d');
        if (($startDate > $currentDate && $currentDate < $endDate) || ($startDate < $currentDate && $currentDate > $endDate)) {
            $isActive = false;
        }
        return $isActive;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
            if (empty($this->isConfirmed)) {
                $this->isConfirmed = false;
            }
            if ($this->isExtra() || $this->course->program->isGroup()) {
                $this->isAutoRenew = false;
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (
            $this->course->program->isGroup() || (!empty($this->rescheduleBeginDate))
            || (!$insert) || $this->isExtra()
        ) {
            return parent::afterSave($insert, $changedAttributes);
        }
        $interval = new \DateInterval('P1D');
        $start = new \DateTime($this->course->startDate);
        $end = new \DateTime($this->course->endDate);
        $lessonsCount   =   $this->course->lessonsCount;
        $period = new \DatePeriod($start, $interval, $end);
        $this->generateLessonsByCount($start, $lessonsCount);
        return parent::afterSave($insert, $changedAttributes);
    }

    public function generateLessonsByCount($startDate, $lessonsCount, $isConfirmed = null)
    {
        if (!$isConfirmed) {
            $isConfirmed = false;
        }
        $day = $startDate;
        $dayList = Course::getWeekdaysList();
        $weekday = $dayList[$startDate->format('N')];
        for ($x = 1; $x <= $lessonsCount; $x++) {
            $lastLessonDate = $day->format('Y-m-d H:i:s');
            $this->course->createLesson($day, $isConfirmed);
            $day = $day->add(new DateInterval('P7D'));
            if ($this->course->isProfessionalDevelopmentDay($day)) {
                $day = $day->add(new DateInterval('P7D'));
            }
        }
        $this->course->endDate = $lastLessonDate;
        $this->course->save();
        return true;
    }

    public function generateLessons($period, $isConfirmed = null)
    {
        if (!$isConfirmed) {
            $isConfirmed = false;
        }
        foreach ($period as $day) {
            $checkDay = (int) $day->format('N') === (int) $this->courseSchedule->day;
            if ($checkDay) {
                if ($this->course->isProfessionalDevelopmentDay($day)) {
                    continue;
                }
                $this->course->createLesson($day, $isConfirmed, $this->is_online);
            }
        }
        return true;
    }

    public function getPaymentFrequency()
    {
        $paymentFrequency = null;
        switch ($this->paymentFrequencyId) {
            case PaymentFrequency::LENGTH_FULL:
                $paymentFrequency = 'Annually';
                break;
            case PaymentFrequency::LENGTH_HALFYEARLY:
                $paymentFrequency = 'Semi-Annually';
                break;
            case PaymentFrequency::LENGTH_QUARTERLY:
                $paymentFrequency = 'Quarterly';
                break;
            case PaymentFrequency::LENGTH_MONTHLY:
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


    public function deleteUnPaidPaymentCycles()
    {
        if ($this->firstUnpaidPaymentCycle) {
            $paymentCycles = Paymentcycle::find()
                ->notDeleted()
                ->andWhere(['enrolmentId' => $this->id])
                ->andWhere(['>=', 'startDate', $this->firstUnpaidPaymentCycle->startDate])
                ->all();
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

    public function resetPaymentRequest()
    {
        if ($this->firstUnpaidPaymentCycle) {
            $this->deletePaymentRequest();
            $startDate = (new \DateTime($this->firstUnpaidPaymentCycle->startDate))->format('Y-m-d');
            $currentDate = new \DateTime();
            $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
            $paymentCycles = PaymentCycle::find()
                ->notDeleted()
                ->andWhere(['enrolmentId' => $this->id])
                ->andWhere(['between', 'startDate', $startDate, $priorDate])
                ->all();

            foreach ($paymentCycles as $paymentCycle) {
                $startDate = new \DateTime($paymentCycle->startDate);
                $endDate = new \DateTime($paymentCycle->endDate);
                $dateRange = $startDate->format('Y-m-d') . ' - ' . $endDate->format('Y-m-d');
                $this->createPaymentRequest($dateRange);
            }
        }
        return true;
    }

    public function deletePaymentRequest()
    {
        $lessons = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->notCanceled()
            ->andWhere(['courseId' => $this->course->id])
            ->all();

        foreach ($lessons as $lesson) {
            if ($lesson->hasPaymentRequest()) {
                $lesson->paymentRequest->delete();
            }
        }
        return true;
    }

    public function resetPaymentCycle()
    {
        $endDate = new \DateTime();
        if ($this->firstUnpaidPaymentCycle) {
            $startDate = new \DateTime($this->firstUnpaidPaymentCycle->startDate);
            $enrolmentLastPaymentCycleEndDate = new \DateTime($this->course->endDate);
            $intervalMonths = $this->diffInMonths($startDate, $enrolmentLastPaymentCycleEndDate);
            $this->deleteUnPaidPaymentCycles();
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

    public function hasPaymentCycles()
    {
        return !empty($this->paymentCycles);
    }

    public function setPaymentCycle($startDate)
    {
        if (!$this->hasPaymentCycles()) {
            if ((new \DateTime($startDate))->format('Y-m-1') > (new \DateTime($this->course->startDate))->format('Y-m-1')) {
                $paymentCycle              = new PaymentCycle();
                $paymentCycle->enrolmentId = $this->id;
                $paymentCycle->startDate   = (new \DateTime($this->course->startDate))->format('Y-m-1');
                $paymentCycle->id          = null;
                $paymentCycle->isNewRecord = true;
                $endDate = (new \DateTime($startDate))->format('Y-m-1');
                $paymentCycle->endDate     =  (new \DateTime($endDate))->modify('Last day of last month')->format('Y-m-d');
                $paymentCycle->save();
            }

            $enrolmentStartDate      = new \DateTime($startDate);
            $paymentCycleStartDate   = \DateTime::createFromFormat('Y-m-d', $enrolmentStartDate->format('Y-m-1'));
            for ($i = 0; $i <= (int) 24 / $this->paymentsFrequency->frequencyLength; $i++) {
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
        return true;
    }

    public function setDueDate()
    {
        $lessons = Lesson::find()
            ->enrolment($this->id)
            ->isConfirmed()
            ->notCanceled()
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->paymentCycle) {
                $firstLessonDate = $lesson->paymentCycle->firstLesson->getOriginalDate();
                $dueDate = carbon::parse($firstLessonDate)->modify('first day of previous month');
                $dueDate = carbon::parse($dueDate)->modify('+ 14 day')->format('Y-m-d');
                $lesson->updateAttributes(['dueDate' => $dueDate]);
            }
        }
        return true;
    }

    public function hasExplodedLesson()
    {
        $lessonSplits = Lesson::find()
            ->split()
            ->unscheduled()
            ->enrolment($this->id)
            ->all();

        return !empty($lessonSplits);
    }

    public function isExtra()
    {
        return $this->course->type === self::TYPE_EXTRA;
    }

    public function getEnrolmentAmount()
    {
        $programRate = $this->course->courseProgramRate->programRate;
        $duration = $this->course->getDuration();
        $totalAmount = $programRate * $duration * $this->paymentFrequencyId * 4;
        return $totalAmount;
    }

    public function setStatus()
    {
        $studentStatus = Student::STATUS_INACTIVE;
        $customerStatus = USER::STATUS_NOT_ACTIVE;
        foreach ($this->student->enrolments as $enrolment) {
            if ($enrolment->isActive()) {
                $studentStatus = Student::STATUS_ACTIVE;
                $customerStatus = USER::STATUS_ACTIVE;
                break;
            }
        }
        $this->student->updateAttributes([
            'status' => $studentStatus,
            'isDeleted' => false
        ]);
        $this->customer->updateAttributes([
            'status' => $customerStatus,
            'isDeleted' => false
        ]);
        return true;
    }

    public function hasPaymentFrequencyDiscount()
    {
        return !empty($this->paymentFrequencyDiscount);
    }

    public function hasMultiEnrolmentDiscount()
    {
        return !empty($this->multipleEnrolmentDiscount);
    }

    public function getPaymentFrequencyDiscountValue()
    {
        if (!$this->paymentFrequencyDiscount) {
            return 'Not set';
        }
        return $this->paymentFrequencyDiscount->discount . '%';
    }

    public function getMultipleEnrolmentDiscountValue()
    {
        if (!$this->multipleEnrolmentDiscount) {
            return 'Not set';
        }
        return '$' . $this->multipleEnrolmentDiscount->discount;
    }

    public function hasGroupDiscount()
    {
        return !empty($this->groupDiscount);
    }

    public function getCustomerModeOfPay()
    {
        return $this->paymentsFrequency->frequencyLength == 1 ? 'pays every month'
            : 'pays every ' . $this->paymentsFrequency->frequencyLength . 'month';
    }

    public function getPayment()
    {
        if (!$this->isExtra()) {
            $lessons = $this->course->lessons;
        } else {
            $lessons = $this->course->extraLessons;
        }
        $amount = 0;
        foreach ($lessons as $lesson) {
            $amount += $lesson->getCreditAppliedAmount($this->id);
        }
        return $amount;
    }

    public function getEnrolmentPaymentFrequency()
    {
        return $this->hasOne(EnrolmentPaymentFrequency::className(), ['enrolmentId' => 'id']);
    }

    public function getCustomerRecurringPaymentEnrolment()
    {
        return $this->hasOne(CustomerRecurringPaymentEnrolment::className(), ['enrolmentId' => 'id'])
            ->onCondition(['customer_recurring_payment_enrolment.isDeleted' => false]);
    }


    public function getLastRootLesson()
    {
        return Lesson::find()
            ->isConfirmed()
            ->roots()
            ->andWhere(['lesson.courseId' => $this->courseId])
            ->orderby(['lesson.date' => SORT_DESC])
            ->notDeleted()
            ->one();
    }

    public function extend()
    {
        $lastLesson = $this->lastLesson;
        $interval = new \DateInterval('P1D');
        $start = (new \DateTime($lastLesson->date))->modify('+1 day');
        $end = new \DateTime($this->course->endDate);
        $period = new \DatePeriod($start, $interval, $end);
        $this->generateLessons($period, true);
        $extendedLessons = Lesson::find()->andWhere(['courseId' => $this->course->id])->between($start, $end)->all();
        foreach ($extendedLessons as $extendedLesson) {
            $extendedLesson->setDiscount();
            $extendedLesson->makeAsRoot();
            $emailNotifyTypes = NotificationEmailType::find()->all();
            foreach($emailNotifyTypes as $emailNotifyType) {
                $emailStatus = new PrivateLessonEmailStatus();
                $emailStatus->lessonId = $extendedLesson->id;
                $emailStatus->notificationType = $emailNotifyType->id;
                $emailStatus->status = false;
                $emailStatus->save();
            }
        }
        $paymentCycleStartDate = (new \DateTime($lastLesson->date))->format('Y-m-t');
        $paymentCycleStartDate = (new \DateTime($paymentCycleStartDate))->modify('+1 day')->format('M, Y');
        $enrolmentPaymentFrequency = new EnrolmentPaymentFrequency();
        $enrolmentPaymentFrequency->effectiveDate = clone (new \DateTime($this->lastLesson->date));
        $enrolmentPaymentFrequency->effectiveDate =  $enrolmentPaymentFrequency->effectiveDate->format('Y-m-d');
        $enrolmentPaymentFrequency->enrolmentId = $this->id;
        $enrolmentPaymentFrequency->resetPaymentCycle();
        return true;
    }

    public function resetDiscount($type, $value)
    {
        if ((int) $type === (int) EnrolmentDiscount::TYPE_PAYMENT_FREQUENCY) {
            $type = LessonDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY;
        } else {
            $type = LessonDiscount::TYPE_MULTIPLE_ENROLMENT;
        }
        if ($this->course->isPrivate() && $this->partialyPaidPaymentCycle) {
            Yii::$app->queue->push(new EnrolmentDiscountQueue([
                'courseId' => $this->courseId,
                'type' => $type,
                'value' => $value,
                'enrolmentId' => $this->id,
            ]));

            $lessons = Lesson::find()
                ->notDeleted()
                ->andWhere(['courseId' => $this->courseId])
                ->notCompleted()
                ->isConfirmed()
                ->notCanceled()
                ->joinWith(['privateLesson' => function ($query) {
                    $query->andWhere(['>', 'private_lesson.balance', 0]);
                }])
                ->limit(12)
                ->all();
            foreach ($lessons as $lesson) {
                $lessonDiscount = LessonDiscount::find()
                    ->andWhere(['type' => $type, 'lessonId' => $lesson->id, 'enrolmentId' => $this->id])
                    ->one();
                if ($lessonDiscount) {
                    if ($lessonDiscount->isPfDiscount()) {
                        $lessonDiscount->value = $value;
                    } else {
                        $lessonDiscount->value = $value / 4;
                    }
                    $lessonDiscount->save();
                } else {
                    $lessonDiscount = new LessonDiscount();
                    $lessonDiscount->lessonId = $lesson->id;
                    if ((int) $type === (int) LessonDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY) {
                        $lessonDiscount->type = LessonDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY;
                        $lessonDiscount->value = $value;
                        $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_PERCENTAGE;
                    } else {
                        $lessonDiscount->type = LessonDiscount::TYPE_MULTIPLE_ENROLMENT;
                        $lessonDiscount->value = $value / 4;
                        $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_DOLLAR;
                    }
                    $lessonDiscount->enrolmentId = $this->id;
                    $lessonDiscount->save();
                }
            }
        }
        return true;
    }

    public function resetGroupDiscount()
    {
        $type = LessonDiscount::TYPE_GROUP;
        $fromDate = new \DateTime($this->firstUnpaidLesson->date);
        $toDate = new \DateTime($this->lastLesson->date);
        $lessons = Lesson::find()
            ->notDeleted()
            ->andWhere(['courseId' => $this->courseId])
            ->between($fromDate, $toDate)
            ->isConfirmed()
            ->notCanceled()
            ->all();
        foreach ($lessons as $lesson) {
            $lessonDiscount = LessonDiscount::find()
                ->andWhere(['type' => $type, 'lessonId' => $lesson->id, 'enrolmentId' => $this->id])
                ->one();
            if (!$lessonDiscount) {
                $lessonDiscount = new LessonDiscount();
                $lessonDiscount->lessonId = $lesson->id;
                $lessonDiscount->type = LessonDiscount::TYPE_GROUP;
                $lessonDiscount->enrolmentId = $this->id;
            }
            if ((int) $this->groupDiscount->discountType === (int) EnrolmentDiscount::VALUE_TYPE_PERCENTAGE) {
                $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_PERCENTAGE;
                $lessonDiscount->value = $this->groupDiscount->discount;
            } else {
                $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_DOLLAR;
                $lessonDiscount->value = $this->groupDiscount->discount / count($this->course->lessons);
            }
            $lessonDiscount->save();
        }
        return true;
    }

    public function getPreviewDataProvider()
    {
        $objects = ["Lesson's Discount"];
        $classes = ["lesson-discount"];
        if ($this->course->isPrivate()) {
            $startDate = Carbon::parse($this->partialyPaidPaymentCycle->startDate)->format('M d, Y');
            $endDate = Carbon::parse($this->lastPaymentCycle->endDate)->format('M d, Y');
            array_merge($objects, ["Payment Cycles", "Payment Request"]);
            array_merge($classes, ["payment-cycle", "payment-request"]);
        } else {
            $startDate = Carbon::parse($this->firstUnpaidLesson->date)->format('M d, Y');
            $endDate = Carbon::parse($this->lastLesson->date)->format('M d, Y');
        }
        $dates = [$startDate, $endDate];
        $dateRange = implode(' - ', $dates);
        foreach ($objects as $i => $value) {
            $results[] = [
                'objects' => $value,
                'action' => 'will be modified',
                'date_range' => 'within ' . $dateRange,
                'class' => $classes[$i]
            ];
        }
        return new ArrayDataProvider([
            'allModels' => $results,
            'sort' => [
                'attributes' => ['objects', 'action', 'date_range', 'class']
            ]
        ]);
    }

    public function shrink()
    {
        $this->addCreditInvoice($this->course->endDate);
        $this->updateAttributes(['isAutoRenew' => false]);
        $this->resetPaymentCycle();
        $this->course->updateDates();
        return true;
    }

    public function deleteWithOutTransactionalData()
    {
        return $this->delete();
    }

    public function deleteWithTransactionalData()
    {
        $lessons = Lesson::find()
            ->andWhere(['courseId' => $this->courseId])
            ->isConfirmed()
            ->all();
        foreach ($this->paymentCycles as $paymentCycle) {
            if ($paymentCycle->hasProformaInvoice()) {
                $pfi = $paymentCycle->proFormaInvoice;
                if ($pfi->hasPayments()) {
                    foreach ($pfi->payments as $payment) {
                        $payment->delete();
                    }
                }
                $paymentCycle->proFormaInvoice->delete();
            }
            $paymentCycle->delete();
        }
        foreach ($lessons as $lesson) {
            if ($lesson->hasInvoice()) {
                $invoice = $lesson->invoice;
                if ($invoice->hasPayments()) {
                    foreach ($invoice->payments as $payment) {
                        $payment->delete();
                    }
                }
                $invoice->delete();
            }
            if ($lesson->hasCreditUsed($this->id)) {
                $payments = $lesson->getCreditUsedPayment($this->id);
                if ($payments) {
                    $payment = current($payments);
                    $creditPayment = $payment->debitUsage->creditUsagePayment;
                    if ($creditPayment->isInvoicePayment()) {
                        if (!$creditPayment->invoice->isDeleted) {
                            $creditPayment->invoice->delete();
                        }
                    }
                }
            }
            if ($lesson->isExtra() && $lesson->hasProFormaInvoice()) {
                $pfi = $lesson->proFormaInvoice;
                if ($pfi->hasPayments()) {
                    foreach ($pfi->payments as $payment) {
                        $payment->delete();
                    }
                }
                $pfi->delete();
            }
            $lesson->delete();
        }
        return $this->delete();
    }
    public function getEnrolmentDiscount()
    {
        return $this->hasMany(EnrolmentDiscount::className(), ['enrolmentId' => 'id']);
    }

    public function getLessonPayment()
    {
        return $this->hasOne(LessonPayment::className(), ['enrolmentId' => 'id'])
            ->onCondition(['lesson_payment.isDeleted' => false]);
    }

    public function getLessonPayments()
    {
        return $this->hasMany(LessonPayment::className(), ['enrolmentId' => 'id']);
    }

    public function hasPayment()
    {
        return $this->lessonPayment;
    }

    public function setAutoRenewalPaymentCycle($startDate, $autoRenewalId)
    {
        $enrolmentStartDate      = new \DateTime($startDate);
        $endDate = (new \DateTime($startDate))->format('Y-m-1');
        $paymentCycleStartDate   = \DateTime::createFromFormat('Y-m-d', $enrolmentStartDate->format('Y-m-1'));
        for ($i = 0; $i <= (int) 23 / $this->paymentsFrequency->frequencyLength; $i++) {
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
            $paymentCycle->save();
            $autoRenewalPaymentCycle = new AutoRenewalPaymentCycle();
            $autoRenewalPaymentCycle->autoRenewalId = $autoRenewalId;
            $autoRenewalPaymentCycle->paymentCycleId = $paymentCycle->id;
            $autoRenewalPaymentCycle->save();
        }
        return true;
    }

    public function triggerPusher()
    {
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
        $this->updateAttributes(['isEnableInfo' =>false]);
        $this->updateAttributes(['isEnableRescheduleInfo' => false]);
        return $pusher->trigger('enrolment', 'lesson-confirm', $this->id);
    }
}
