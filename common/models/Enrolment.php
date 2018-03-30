<?php

namespace common\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use Carbon\Carbon;
use common\models\discount\EnrolmentDiscount;

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
    
    const AUTO_RENEWAL_DAYS_FROM_END_DATE = 90;
    const AUTO_RENEWAL_STATE_ENABLED='enabled';
    const AUTO_RENEWAL_STATE_DISABLED='disabled';
    const TYPE_REGULAR = 1;
    const TYPE_EXTRA   = 2;
    const TYPE_REVERSE = 'reverse';
    const ENROLMENT_EXPIRY=90;
    
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
            [['paymentFrequencyId', 'isDeleted', 'isConfirmed',
                'hasEditable', 'isAutoRenew', 'applyFullDiscount'], 'safe'],
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
            'isAutoRenew' => 'Auto Renew'
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

    public function getPaymentFrequencyDiscount()
    {
        return $this->hasOne(EnrolmentDiscount::className(), ['enrolmentId' => 'id'])
            ->onCondition(['type' => EnrolmentDiscount::TYPE_PAYMENT_FREQUENCY]);
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

    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customer_id'])
            ->via('student');
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
            ->via('lessons');
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
            ->joinWith(['course' => function ($query) {
                $query->joinWith(['enrolment' =>function ($query) {
                    $query->andWhere(['enrolment.id' => $this->id]);
                }])
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
            ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_GROUP_LESSON,
                'invoice_line_item.isDeleted' => false]);
    }
    
    public function getPrivateLessonLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
            ->via('lineItemPaymentCycleLessons')
            ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON,
                'invoice_line_item.isDeleted' => false]);
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
            return $this->hasOne(PaymentCycle::className(), ['enrolmentId' => 'id'])
                ->where(['>', 'startDate', (new \DateTime())->format('Y-m-d')]);
        }
    }

    public function getNextPaymentCycle()
    {
        $currentPaymentCycleEndDate = new \DateTime($this->currentPaymentCycle->endDate);
        return $this->hasOne(PaymentCycle::className(), ['enrolmentId' => 'id'])
            ->where(['>', 'startDate', $currentPaymentCycleEndDate->format('Y-m-d')]);
    }

    public function getFirstLesson()
    {
        return $this->hasOne(Lesson::className(), ['courseId' => 'courseId'])
            ->onCondition(['lesson.isDeleted' => false, 'lesson.isConfirmed' => true])
            ->orderBy(['date' => SORT_ASC]);
    }

    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'courseId'])
                ->onCondition(['lesson.isDeleted' => false, 'lesson.isConfirmed' => true,
                    'lesson.status' => [Lesson::STATUS_RESCHEDULED, Lesson::STATUS_SCHEDULED,
                        Lesson::STATUS_UNSCHEDULED]]);
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

    public function getFirstUnPaidProFormaPaymentCycle()
    {
        foreach ($this->paymentCycles as $paymentCycle) {
            if (!$paymentCycle->hasProFormaInvoice()) {
                return $paymentCycle;
            } elseif (!$paymentCycle->proFormaInvoice->isPaid() &&
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
            } elseif (!$paymentCycle->proFormaInvoice->isPaid() &&
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

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
            if (empty($this->isConfirmed)) {
                $this->isConfirmed = false;
            }
            if ($this->isExtra() || $this->course->program->isGroup()) {
                $renew = false;
            } else {
                $renew = true;
            }
            $this->isAutoRenew = $renew;
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->course->program->isGroup() || (!empty($this->rescheduleBeginDate))
            || (!$insert) || $this->isExtra()) {
            return parent::afterSave($insert, $changedAttributes);
        }
        $interval = new \DateInterval('P1D');
        $start = new \DateTime($this->course->startDate);
        $end = new \DateTime($this->course->endDate);
        $period = new \DatePeriod($start, $interval, $end);
        $this->generateLessons($period);
        return parent::afterSave($insert, $changedAttributes);
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
                $this->course->createLesson($day, $isConfirmed);
            }
        }
        return true;
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
            $startDate = \DateTime::createFromFormat(
        
                'Y-m-d',
                $this->firstUnPaidProFormaPaymentCycle->startDate
        
            );
            $enrolmentLastPaymentCycleEndDate = \DateTime::createFromFormat(
        
                'Y-m-d H:i:s',
                    $this->course->endDate
        
            );
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

    public function setPaymentCycle($startDate)
    {
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

    public function getLastRootLesson()
    {
        return Lesson::find()
            ->isConfirmed()
            ->roots()
            ->andWhere(['lesson.courseId' => $this->courseId])
            ->orderby(['lesson.date' => SORT_DESC])
            ->one();
    }

    public function extend()
    {
        $lastLesson = $this->lastRootLesson;
        $interval = new \DateInterval('P1D');
        $start = (new \DateTime($lastLesson->date))->modify('+1 day');
        $end = new \DateTime($this->course->endDate);
        $period = new \DatePeriod($start, $interval, $end);
        $this->generateLessons($period, true);
        $this->resetPaymentCycle();
        return true;
    }

    public function shrink()
    {
        $startDate = null;
        $invoice = $this->addCreditInvoice($startDate, $this->course->endDate);
        return $invoice;
    }

    public function deleteWithOutTransactionalData()
    {
        return $this->delete();
    }

    public function deleteWithTransactionalData()
    {
        $lessons = Lesson::find()
                ->where(['courseId' => $this->courseId])
                ->isConfirmed()
                ->all();
        foreach ($this->paymentCycles as $paymentCycle) {
            if ($paymentCycle->hasProformaInvoice()) {
                $paymentCycle->proFormaInvoice->delete();
            }
            $paymentCycle->delete();
        }
        foreach ($lessons as $lesson) {
            if ($lesson->hasInvoice()) {
                $lesson->invoice->delete();
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
            $lesson->delete();
        }
        return $this->delete();
    }
}
