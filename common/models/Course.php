<?php

namespace common\models;

use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Lesson;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeInclusive;
use Yii;
/**
 * This is the model class for table "course".
 *
 * @property string $id
 * @property string $programId
 * @property string $teacherId
 * @property string $locationId
 * @property string $day
 * @property string $fromTime
 * @property string $duration
 * @property string $startDate
 * @property string $endDate
 */
class Course extends \yii\db\ActiveRecord
{
	const EVENT_VACATION_CREATE_PREVIEW = 'vacation-create-preview';
	const EVENT_VACATION_DELETE_PREVIEW = 'vacation-delete-preview';
	const SCENARIO_GROUP_COURSE = 'group-course';
	const SCENARIO_EDIT_ENROLMENT = 'edit-enrolment';

	public $lessonStatus;
    public $studentId;
    public $paymentFrequency;
	public $rescheduleBeginDate;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programId', 'teacherId'], 'required'],
            [['day', 'fromTime', 'duration', 'startDate'], 'required', 'except' => self::SCENARIO_GROUP_COURSE],
            [['programId', 'teacherId', 'paymentFrequency'], 'integer'],
            [['paymentFrequency'], 'required', 'when' => function ($model, $attribute) {
                return (int) $model->program->type === Program::TYPE_PRIVATE_PROGRAM;
            },'except' => self::SCENARIO_EDIT_ENROLMENT 
            ],
            [['startDate', 'duration', 'endDate'], 'string'],
            [['locationId', 'rescheduleBeginDate', 'isConfirmed'], 'safe'],
            ['day', 'checkTeacherAvailableDay', 'on' => self::SCENARIO_EDIT_ENROLMENT],
            ['fromTime', 'checkTime', 'on' => self::SCENARIO_EDIT_ENROLMENT],
            ['endDate', 'checkDate', 'on' => self::SCENARIO_EDIT_ENROLMENT],
            ['day', 'checkTeacherAvailableDay', 'on' => self::SCENARIO_GROUP_COURSE],
            ['fromTime', 'checkTime', 'on' => self::SCENARIO_GROUP_COURSE],
        ];
    }

	public function checkTeacherAvailableDay($attribute, $params)
    {
        $teacherAvailabilities = TeacherAvailability::find()
            ->joinWith(['teacher' => function ($query) {
                $query->where(['user.id' => $this->teacherId]);
            }])
                ->where(['teacher_availability_day.day' => $this->day])
                ->all();
        if (empty($teacherAvailabilities)) {
			$dayList = self::getWeekdaysList();
			$day = $dayList[$this->day];
            $this->addError($attribute, 'Teacher is not available on '. $day);
        }
    }

	public function checkDate($attribute, $params)
	{
		$oldEndDate = (new \DateTime($this->getOldAttribute('endDate')))->format('d-m-Y');
		$endDate = (new \DateTime($this->endDate))->format('d-m-Y');
		if ($endDate > $oldEndDate) {
			return $this->addError($attribute, 'End date must be less than course end date');
		}
	}
	
	public function checkTime($attribute, $params)
    {
        $teacherAvailabilities = TeacherAvailability::find()
            ->joinWith(['teacher' => function ($query) {
                $query->where(['user.id' => $this->teacherId]);
            }])
                ->where(['teacher_availability_day.day' => $this->day])
                ->all();
        $availableHours = [];
        if (! empty($teacherAvailabilities)) {
            foreach ($teacherAvailabilities as $teacherAvailability) {
                $start = new \DateTime($teacherAvailability->from_time);
                $end = new \DateTime($teacherAvailability->to_time);
                $interval = new \DateInterval('PT15M');
                $hours = new \DatePeriod($start, $interval, $end);
                foreach ($hours as $hour) {
                    $availableHours[] = Yii::$app->formatter->asTime($hour);
                }
            }
            $fromTime = (new \DateTime($this->fromTime))->format('h:i A');
            if (!in_array($fromTime, $availableHours)) {
                $this->addError($attribute, 'Please choose the lesson time within the teacher\'s availability hours');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'programId' => 'Program Name',
            'teacherId' => 'Teacher Name',
            'locationId' => 'Location Name',
            'day' => 'Day',
            'fromTime' => 'From Time',
            'duration' => 'Duration',
            'startDate' => 'Start Date',
            'endDate' => 'End Date',
            'paymentFrequency' => 'Payment Frequency',
            'rescheduleBeginDate' => 'Reschedule Future Lessons From',
            'rescheduleFromDate' => 'With effects from',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\CourseQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\CourseQuery(get_called_class());
    }

    public static function getWeekdaysList()
    {
        return [
        1 => 'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        ];
    }

	
    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacherId']);
    }

    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'programId']);
    }

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['courseId' => 'id']);
    }

	public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'locationId']);
    }

    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'id']);
    }

    public function getEnrolments()
    {
        return $this->hasMany(Enrolment::className(), ['courseId' => 'id']);
    }
    public function getEnrolmentsCount()
    {
        return $this->getEnrolments()->count();
    }

    public static function lessonStatuses()
    {
        return [
            'all' => 'All',
            Lesson::STATUS_UNSCHEDULED => 'Unscheduled',
        ];
    }
    public function beforeSave($insert)
    {
		if(!$insert) {
        	return parent::beforeSave($insert);
		}
        $fromTime = \DateTime::createFromFormat('h:i:s', $this->fromTime);
        $this->fromTime = $fromTime->format('H:i:s');
        $timebits = explode(':', $this->fromTime);
		$this->isConfirmed = false;
        if ((int) $this->program->type === Program::TYPE_GROUP_PROGRAM) {
            $startDate = new \DateTime($this->startDate);
            $this->startDate = $startDate->format('Y-m-d H:i:s');
            $endDate = new \DateTime($this->endDate);
            $this->endDate = $endDate->format('Y-m-d 00:00:00');
        } else {
            $endDate = \DateTime::createFromFormat('d-m-Y', $this->startDate);
            $startDate = new \DateTime($this->startDate);
            $startDate->add(new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'));
            $this->startDate = $startDate->format('Y-m-d H:i:s');
            $endDate->add(new \DateInterval('P1Y'));
            $this->endDate = $endDate->format('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
		if(!$insert) {
        	return parent::afterSave($insert, $changedAttributes);
		}
        if ((int) $this->program->type === Program::TYPE_PRIVATE_PROGRAM) {
            $enrolmentModel = new Enrolment();
            $enrolmentModel->courseId = $this->id;
            $enrolmentModel->studentId = $this->studentId;
            $enrolmentModel->paymentFrequencyId = $this->paymentFrequency;
            $enrolmentModel->save();
        }
        if ((int) $this->program->type === Program::TYPE_GROUP_PROGRAM) {
            $interval = new \DateInterval('P1D');
            $startDate = $this->startDate;
            $endDate = $this->endDate;
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
			$end->modify('+1 day');
            $period = new \DatePeriod($start, $interval, $end);

            foreach ($period as $day) {
                $professionalDevelopmentDay = clone $day;
                $professionalDevelopmentDay->modify('last day of previous month');
                $professionalDevelopmentDay->modify('fifth '.$day->format('l'));
                if ($day->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
                    continue;
                }
                if ((int) $day->format('N') === (int) $this->day) {
                    $lesson = new Lesson();
                    $lesson->setAttributes([
                        'courseId' => $this->id,
                        'teacherId' => $this->teacherId,
                        'status' => Lesson::STATUS_DRAFTED,
                        'date' => $day->format('Y-m-d H:i:s'),
                        'duration' => $this->duration,
                        'isDeleted' => false,
                    ]);
                    $lesson->save();
                }
            }
        }
        	return parent::afterSave($insert, $changedAttributes);
    }

	public function generateLessons($lessons, $startDate)
	{
		$lessonTime								 = (new \DateTime($this->startDate))->format('H:i:s');
		$duration								 = explode(':', $lessonTime);
		$nextWeekScheduledDate = $startDate;
		$dayList = self::getWeekdaysList();
		$day = $dayList[$this->day];
		foreach ($lessons as $lesson) {
			if ($this->isProfessionalDevelopmentDay($startDate)) {
				$nextWeekScheduledDate = $startDate->modify('next '.$day);
			}
			$originalLessonId	 = $lesson->id;
			$lesson->id			 = null;
			$lesson->isNewRecord = true;
			$lesson->status		 = Lesson::STATUS_DRAFTED;
			$nextWeekScheduledDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
			$lesson->date		 = $nextWeekScheduledDate->format('Y-m-d H:i:s');
			$lesson->save();

			$startDate->modify('next '.$day);
		}
	}

	public function isProfessionalDevelopmentDay($startDate)
	{
		$dayList = self::getWeekdaysList();
		$day = $dayList[$this->day];
		$isProfessionalDevelopmentDay = false;
		$professionalDevelopmentDay = clone $startDate;
		$professionalDevelopmentDay->modify('last day of previous month');
		$professionalDevelopmentDay->modify('fifth '.$day);
		if ($startDate->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
			$isProfessionalDevelopmentDay = true;
		}
		return $isProfessionalDevelopmentDay;
	}

	public function pushLessons($fromDate, $toDate)
	{
		$fromDate	 = (new \DateTime($fromDate))->format('Y-m-d');
		$lessons	 = Lesson::find()
			->where([
				'courseId' => $this->id,
				'lesson.status' => Lesson::STATUS_SCHEDULED
			])
			->andWhere(['>=', 'date', $fromDate])
			->all();
		$dayList = self::getWeekdaysList();
		$day = $dayList[$this->day];
		$startDate	 = new \DateTime($toDate);
		$startDate->modify('next '.$day);
		$this->generateLessons($lessons, $startDate);
	}

	public function restoreLessons($fromDate, $toDate)
	{
		$toDate		 = (new \DateTime($toDate))->format('Y-m-d');
		$lessons	 = Lesson::find()
			->where([
				'courseId' => $this->id,
				'lesson.status' => Lesson::STATUS_SCHEDULED
			])
			->andWhere(['>=', 'date', $toDate])
			->all();
		$dayList = self::getWeekdaysList();
		$day = $dayList[$this->day];
		$startDay	 = (new \DateTime($fromDate))->format('l');
		if ($day !== $startDay) {
			$startDate = new \DateTime($fromDate);
			$startDate->modify('next '.$day);
		} else {
			$startDate	 = (new \DateTime($fromDate))->format('Y-m-d');
			$startDate = new \DateTime($startDate);
		}
		$this->generateLessons($lessons, $startDate);
	}
}
