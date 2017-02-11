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
	const STATUS_UNSCHEDULED = 5;
	const STATUS_MISSED = 6;
	
    const SCENARIO_REVIEW = 'review';
    const SCENARIO_PRIVATE_LESSON = 'private-lesson';
    const SCENARIO_EDIT_REVIEW_LESSON = 'edit-review-lesson';
    const SCENARIO_LESSON_CREATE = 'lesson-create';

    public $programId;
    public $time;
    public $hours;
    public $program_name;
	public $showAllReviewLessons = false;
	public $present;
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
            [['courseId', 'teacherId', 'status', 'isDeleted', 'duration', 'date'], 'required'],
            [['courseId', 'status'], 'integer'],
            [['date', 'programId','colorCode', 'classroomId'], 'safe'],
            ['date', 'checkRescheduleLessonTime', 'on' => self::SCENARIO_EDIT_REVIEW_LESSON],
            [['date'], 'checkConflict', 'on' => self::SCENARIO_REVIEW],
            ['date', 'checkRescheduleLessonTime', 'on' => self::SCENARIO_PRIVATE_LESSON],
            ['date', 'checkLessonConflict', 'on' => self::SCENARIO_PRIVATE_LESSON],
            ['date', 'checkDateConflict', 'on' => self::SCENARIO_PRIVATE_LESSON],
            ['teacherId', 'checkRescheduleLessonTime', 'on' => self::SCENARIO_PRIVATE_LESSON],
			['date', 'checkRescheduleLessonTime', 'on' => self::SCENARIO_LESSON_CREATE],
            ['date', 'checkLessonConflict', 'on' => self::SCENARIO_LESSON_CREATE],
            ['date', 'checkDateConflict', 'on' => self::SCENARIO_LESSON_CREATE],
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
		$oldDate = $this->getOldAttribute('date');
		$oldTeacherId = $this->getOldAttribute('teacherId'); 
        if ((!empty($conflictedLessonIds))) {
			if(new \DateTime($oldDate) != new \DateTime($this->date)) {
            	$this->addError($attribute, 'Lesson date conflicts with another lesson');
			} else {
            	$this->addError($attribute, 'Teacher occupied with another lesson');
			}
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
		$oldDate = $this->getOldAttribute('date');
		$oldTeacherId = $this->getOldAttribute('teacherId'); 
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
				if(new \DateTime($oldDate) != new \DateTime($this->date)) {
                	$this->addError($attribute, 'Please choose the lesson time within the teacher\'s availability hours');
				} else {
            		$this->addError($attribute, 'Teacher is not available at ' . Yii::$app->formatter->asTime($this->date));
				}
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

        $intervals = [];
        foreach ($holidays as $holiday) {
            $intervals[] = new DateRangeInclusive(new \DateTime($holiday->date), new \DateTime($holiday->date));
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
				if(new \DateTime($studentLesson->date) == new \DateTime($this->date) && (int)$studentLesson->status === Lesson::STATUS_SCHEDULED){
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
			$oldDate = $this->getOldAttribute('date');
			$oldTeacherId = $this->getOldAttribute('teacherId'); 
			if((int)$oldTeacherId == $this->teacherId && $oldDate == new \DateTime($this->date)) {
				if(new \DateTime($teacherLesson->date) == new \DateTime($this->date) && (int)$teacherLesson->status === Lesson::STATUS_SCHEDULED){
					continue;
				}
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
            'colorCode' => 'Color Code',
			'classroomId' => 'Classroom'
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

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->viaTable('invoice_line_item', ['item_id' => 'id'])
            ->onCondition(['invoice.type' => Invoice::TYPE_INVOICE, 'invoice.isDeleted' => false]);
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

    public function getProFormaInvoiceLineItem()
    {
        foreach ($this->invoiceLineItems as $invoiceLineItem) {
            if ($invoiceLineItem->invoice->isProFormaInvoice()) {
                return  $invoiceLineItem;
            }
        }
        return null;
    }

    public function getInvoiceLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['item_id' => 'id'])
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
							'teacherId' => $this->getOldAttribute('teacherId')
                    	]);	
					}
                    
                    $originalLessonId = $this->id;
                    $this->id = null;
                    $this->isNewRecord = true;
					if(isset($changedAttributes['date']) && !empty($this->date)) {
                    	$this->date = $toDate->format('Y-m-d H:i:s');
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
}
