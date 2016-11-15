<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeInclusive;

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

    const SCENARIO_REVIEW = 'review';
    const SCENARIO_PRIVATE_LESSON = 'private-lesson';

    public $programId;
    public $time;
    public $hours;
    public $program_name;
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
            [['date', 'programId', 'notes', 'teacherId'], 'safe'],
            ['date', 'checkRescheduleLessonTime', 'on' => self::SCENARIO_REVIEW],
            [['date'], 'checkConflict', 'on' => self::SCENARIO_REVIEW],
            ['date', 'checkRescheduleLessonTime', 'on' => self::SCENARIO_PRIVATE_LESSON],
            ['date', 'checkLessonConflict', 'on' => self::SCENARIO_PRIVATE_LESSON],
            ['date', 'checkDateConflict', 'on' => self::SCENARIO_PRIVATE_LESSON],
        ];
    }

    public function checkLessonConflict($attribute, $params)
	{
		$lessonIntervals = $this->lessonIntervals();
        $tree = new IntervalTree($lessonIntervals);
        $conflictedLessonIds = [];
        $conflictedLessonsResults = $tree->search(new \DateTime($this->date));
        foreach ($conflictedLessonsResults as $conflictedLessonsResult) {
            $conflictedLessonIds[] = $conflictedLessonsResult->id;
        }
        if ((!empty($conflictedLessonIds))) {
            $this->addError($attribute, 'Lesson date conflicts with another lesson');
        }
	}

	public function checkDateConflict($attribute, $params)
	{
		$intervals = $this->dateIntervals();
        $tree = new IntervalTree($intervals);
        $conflictedDates = [];
        $conflictedDatesResults = $tree->search(new \DateTime($this->date));
        foreach ($conflictedDatesResults as $conflictedDatesResult) {
            $startDate = $conflictedDatesResult->getStart();
            $conflictedDates[] = $startDate->format('Y-m-d');
        }
        if (!empty($conflictedDates)) {
            $this->addError($attribute, 'Lesson time conflicts with holiday');
        }
	}

    public function checkRescheduleLessonTime($attribute, $params)
    {
        $day = (new \DateTime($this->date))->format('N');
        $teacherAvailabilities = TeacherAvailability::find()
            ->joinWith(['teacher' => function ($query) {
                $query->where(['user.id' => $this->teacherId]);
            }])
                ->where(['teacher_availability_day.day' => $day])
                ->all();
        $availableHours = [];
        if (empty($teacherAvailabilities)) {
            $this->addError($attribute, 'Teacher is not available on '.(new \DateTime($this->date))->format('l'));
        } else {
            foreach ($teacherAvailabilities as $teacherAvailability) {
                $start = new \DateTime($teacherAvailability->from_time);
                $end = new \DateTime($teacherAvailability->to_time);
                $interval = new \DateInterval('PT15M');
                $hours = new \DatePeriod($start, $interval, $end);
                foreach ($hours as $hour) {
                    $availableHours[] = Yii::$app->formatter->asTime($hour);
                }
            }
            $lessonTime = (new \DateTime($this->date))->format('h:i A');
            if (!in_array($lessonTime, $availableHours)) {
                $this->addError($attribute, 'Please choose the lesson time within the teacher\'s availability hours');
            }
        }
    }

    public function checkConflict($attribute, $params)
    {
        $intervals = $this->dateIntervals();
        $tree = new IntervalTree($intervals);
        $conflictedDates = [];
        $conflictedDatesResults = $tree->search(new \DateTime($this->date));
        foreach ($conflictedDatesResults as $conflictedDatesResult) {
            $startDate = $conflictedDatesResult->getStart();
            $conflictedDates[] = $startDate->format('Y-m-d');
        }
        $lessonIntervals = $this->lessonIntervals();
        $tree = new IntervalTree($lessonIntervals);
        $conflictedLessonIds = [];
        $conflictedLessonsResults = $tree->search(new \DateTime($this->date));
        foreach ($conflictedLessonsResults as $conflictedLessonsResult) {
            $conflictedLessonIds[] = $conflictedLessonsResult->id;
        }
        if ((!empty($conflictedDates)) || (!empty($conflictedLessonIds))) {
            $this->addError($attribute, [
               'lessonIds' => $conflictedLessonIds,
               'dates' => $conflictedDates,
           ]);
        }
    }

    public function dateIntervals()
    {
        $holidays = Holiday::find()
            ->all();
        $professionalDevelopmentDays = ProfessionalDevelopmentDay::find()
            ->all();

        $intervals = [];
        foreach ($holidays as $holiday) {
            $intervals[] = new DateRangeInclusive(new \DateTime($holiday->date), new \DateTime($holiday->date));
        }
        foreach ($professionalDevelopmentDays as $professionalDevelopmentDay) {
            $intervals[] = new DateRangeInclusive(new \DateTime($professionalDevelopmentDay->date), new \DateTime($professionalDevelopmentDay->date), null, $this->id);
        }

        return $intervals;
    }

    public function lessonIntervals()
    {
        $locationId = Yii::$app->session->get('location_id');
        $otherLessons = [];
        $intervals = [];

        if ((int) $this->course->program->type === (int) Program::TYPE_PRIVATE_PROGRAM) {
            $studentLessons = self::find()
				->studentLessons($locationId, $this->course->enrolment->student->id)
				
				->all();
            foreach ($studentLessons as $studentLesson) {
				if($studentLesson->date === $this->date && (int)$studentLesson->status === Lesson::STATUS_SCHEDULED){
					continue;
				}
                $otherLessons[] = [
                    'id' => $studentLesson->id,
                    'date' => $studentLesson->date,
                    'duration' => $studentLesson->course->duration,
                ];
            }
        }
        $teacherLessons = self::find()
            ->teacherLessons($locationId, $this->teacherId)
            ->all();
        foreach ($teacherLessons as $teacherLesson) {
			if($teacherLesson->date === $this->date && (int)$teacherLesson->status === Lesson::STATUS_SCHEDULED){
				continue;
			}
            $otherLessons[] = [
                'id' => $teacherLesson->id,
                'date' => $teacherLesson->date,
                'duration' => $teacherLesson->course->duration,
            ];
        }
        $draftLessons = self::find()
            ->where(['courseId' => $this->courseId, 'status' => self::STATUS_DRAFTED])
            ->andWhere(['NOT', ['id' => $this->id]])
            ->all();
        foreach ($draftLessons as $draftLesson) {
            $otherLessons[] = [
                'id' => $draftLesson->id,
                'date' => $draftLesson->date,
                'duration' => $draftLesson->course->duration,
            ];
        }
        foreach ($otherLessons as $otherLesson) {
            $timebits = explode(':', $otherLesson['duration']);
            $intervals[] = new DateRangeInclusive(new \DateTime($otherLesson['date']), new \DateTime($otherLesson['date']), new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'), $otherLesson['id']);
        }

        return $intervals;
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

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->viaTable('invoice_line_item', ['item_id' => 'id'])
            ->onCondition(['invoice.type' => Invoice::TYPE_INVOICE]);
    }

    public function getProFormaInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->viaTable('invoice_line_item', ['item_id' => 'id'])
            ->onCondition(['invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
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
            break;
            case self::STATUS_CANCELED:
                $status = 'Canceled';
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

	public function afterSave($insert, $changedAttributes)
    {
        if ((int) $this->status !== (int) self::STATUS_DRAFTED) {
            if (!$insert) {
                if (isset($changedAttributes['date']) && !empty($this->date)) {
                    $toDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
                    $fromDate = \DateTime::createFromFormat('Y-m-d H:i:s', $changedAttributes['date']);
                    if (!empty($this->teacher->email)) {
                        $this->notifyReschedule($this->teacher, $this->enrolment->course->program, $fromDate, $toDate);
                    }
                    if (!empty($this->enrolment->student->customer->email)) {
                        $this->notifyReschedule($this->enrolment->student->customer, $this->enrolment->program, $fromDate, $toDate);
                    }
                    $this->updateAttributes(['date' => $fromDate->format('Y-m-d H:i:s'),
                        'status' => self::STATUS_CANCELED,
                    ]);
                    $originalLessonId = $this->id;
                    $this->id = null;
                    $this->isNewRecord = true;
                    $this->date = $toDate->format('Y-m-d H:i:s');
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

    public function notifyReschedule($user, $program, $fromDate, $toDate)
    {
        $subject = Yii::$app->name.' - '.$program->name
                .' lesson rescheduled from '.$fromDate->format('d-m-Y h:i a').' to '.$toDate->format('d-m-Y h:i a');

        Yii::$app->mailer->compose('lesson-reschedule', [
            'program' => $program->name,
            'toName' => $user->userProfile->firstname,
            'fromDate' => $fromDate->format('d-m-Y h:i a'),
            'toDate' => $toDate->format('d-m-Y h:i a'),
            ])
            ->setFrom(\Yii::$app->params['robotEmail'])
            ->setTo($user->email)
            ->setSubject($subject)
            ->send();
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
        $rootLesson        = $this->getRootLesson();
        $rootLessonDate    = \DateTime::createFromFormat('Y-m-d H:i:s',
                $rootLesson->date);
        $currentLessonDate = \DateTime::createFromFormat('Y-m-d H:i:s',
                $this->date);
        return $rootLessonDate != $currentLessonDate;
    }

    public function isEnrolmentFirstlesson()
    {
        $courseId             = $this->courseId;
        $enrolmentFirstLesson = self::find()
                ->where(['courseId' => $courseId])
                ->orderBy(['id' => SORT_ASC])
                ->one();
        return $enrolmentFirstLesson->date === $this->date;
    }
}    