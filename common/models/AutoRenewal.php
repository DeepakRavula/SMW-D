<?php

namespace common\models;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use Carbon\Carbon;

use Yii;

/**
 * This is the model class for table "auto_renewal".
 *
 * @property int $id
 * @property int $enrolmentId
 * @property int $paymentFrequency
 * @property string $enrolmentEndDateCurrent
 * @property string $enrolmentEndDateNew
 * @property string $lastPaymentCycleStartDate
 * @property string $lastPaymentCycleEndDate
 * @property string $createdOn
 * @property int $createdByUserId
 */
class AutoRenewal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auto_renewal';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => false,
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => false
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrolmentId', 'paymentFrequency'], 'required'],
            [['enrolmentId', 'paymentFrequency', 'createdByUserId'], 'integer'],
            [['enrolmentEndDateCurrent', 'enrolmentEndDateNew', 'lastPaymentCycleStartDate', 'lastPaymentCycleEndDate', 'createdOn'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enrolmentId' => 'Enrolment ID',
            'paymentFrequency' => 'Payment Frequency',
            'enrolmentEndDateCurrent' => 'Enrolment End Date Current',
            'enrolmentEndDateNew' => 'Enrolment End Date New',
            'lastPaymentCycleStartDate' => 'Last Payment Cycle Start Date',
            'lastPaymentCycleEndDate' => 'Last Payment Cycle End Date',
            'createdOn' => 'Created On',
            'createdByUserId' => 'Created By User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\AutoRenewalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AutoRenewalQuery(get_called_class());
    }

    
    public function renewEnrolment($course)
    {
        $course = Course::findOne($course->id);
        if ($course->enrolment->isAutoRenew) {
            $lastPaymentCycle = $course->enrolment->lastPaymentCycle;
            $lastPaymentCycleStartDate = new Carbon($lastPaymentCycle->startDate);
            $lastPaymentCycleEndDate = new Carbon($lastPaymentCycle->endDate);
            $lastPaymentCycleMonthCount = $lastPaymentCycleEndDate->diffInMonths($lastPaymentCycleStartDate);
            $renewalStartDate = (new Carbon($course->endDate))->addDays(1);
            $renewalEndDate = (new Carbon($course->endDate))->addMonths(24)->endOfMonth();
            $courseProgramRate = new CourseProgramRate();
            $courseProgramRate->courseId = $course->id;
            $courseProgramRate->startDate  = $renewalStartDate->format('Y-m-d');
            $courseProgramRate->endDate = $renewalEndDate->format('Y-m-d');
            $courseProgramRate->programRate = $course->program->rate;
            $courseProgramRate->save();
            $autoRenewal = $this;
            $autoRenewal->enrolmentId = $course->enrolment->id;
            $autoRenewal->enrolmentEndDateCurrent = $course->enrolment->endDateTime;
            $autoRenewal->enrolmentEndDateNew = $renewalEndDate;
            $autoRenewal->paymentFrequency = $course->enrolment->paymentFrequencyId;
            $autoRenewal->lastPaymentCycleStartDate = $course->enrolment->lastPaymentCycle->startDate;
            $autoRenewal->lastPaymentCycleEndDate = $course->enrolment->lastPaymentCycle->endDate;
            $autoRenewal->save();
            
            $interval = new \DateInterval('P1D');
            $start = new \DateTime($renewalStartDate);
            $end = new \DateTime($renewalEndDate);
            $period = new \DatePeriod($start, $interval, $end);
            foreach ($period as $day) {
                $checkDay = (int) $day->format('N') === (int) $course->recentCourseSchedule->day;
                if ($checkDay) {
                    if ($course->isProfessionalDevelopmentDay($day)) {
                        continue;
                    }
                    $createdLesson = $course->createAutoRenewalLesson($day);
                    $autoRenewalLesson = new AutoRenewalLessons();
                    $autoRenewalLesson->autoRenewalId = $autoRenewal->id;
                    $autoRenewalLesson->lessonId = $createdLesson->id;
                    $autoRenewalLesson->save();
                }
               
            }
            $lessonConfirm =  new LessonConfirm();
            $autoRenewalLastLesson = AutoRenewalLessons::find()
                                    ->andWhere(['autoRenewalId' => $autoRenewal->id])
                                    ->orderBy(['auto_renewal_lessons.lessonId' => SORT_DESC])
                                    ->one();
            $lastLessonGenerated = Lesson::findOne($autoRenewalLastLesson->lessonId);
            $lessonConfirm->createCourseSchedule($lastLessonGenerated, $renewalStartDate, $renewalEndDate);
            if ($lastPaymentCycleMonthCount !== $course->enrolment->paymentsFrequency->frequencyLength) {
                $lastPaymentCycle->endDate = $lastPaymentCycleStartDate
                    ->addMonth($course->enrolment->paymentsFrequency->frequencyLength)
                    ->subDay(1);
                $lastPaymentCycle->save();
                $nextPaymentCycleStartDate = (new Carbon($lastPaymentCycle->endDate))->addDay(1);
                $autoRenewalId = $autoRenewal->id;
                $course->enrolment->setAutoRenewalPaymentCycle($nextPaymentCycleStartDate, $autoRenewalId);
            } else {
                $course->enrolment->setAutoRenewalPaymentCycle($renewalStartDate, $autoRenewalId);
            } 
            $course->updateDates();
        }
    }
}
