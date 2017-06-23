<?php

namespace common\models;

use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Lesson;
use Yii;
use common\models\CourseGroup;

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
    const EVENT_CREATE = 'event-create';
    public $lessonStatus;
	public $rescheduleBeginDate;
	public $teacherName;
	public $weeksCount;
	public $lessonsPerWeekCount;
    public $userName;
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
            [['startDate'], 'required', 'except' => self::SCENARIO_GROUP_COURSE],
            [['startDate', 'endDate'], 'safe', 'on' => self::SCENARIO_GROUP_COURSE],
            [['programId', 'teacherId', 'weeksCount', 'lessonsPerWeekCount'], 'integer'],
            [['locationId', 'rescheduleBeginDate', 'isConfirmed'], 'safe'],
          
        ];
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

    public function getStudentEnrolment($student)
    {
        return Enrolment::find()
            ->notDeleted()
            ->isConfirmed()
            ->andWhere(['courseId' => $this->id])
            ->andWhere(['studentId' => $student->id])
            ->one();
    }

    public function getEnrolment()
    {
	    return $this->hasOne(CourseSchedule::className(), ['courseId' => 'id']);
    }

	public function getCourseGroup()
    {
	    return $this->hasOne(CourseGroup::className(), ['courseId' => 'id']);
    }
	
	public function getGroupCourseSchedule()
    {
	    return $this->hasMany(CourseSchedule::className(), ['courseId' => 'id']);
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
		$this->isConfirmed = false;
        if ((int) $this->program->isGroup()) {
			list($firstLessonDate,$secondLessonDate) = $this->startDate;
			if((int)$this->lessonsPerWeekCount === CourseGroup::LESSONS_PER_WEEK_COUNT_ONE) {
				$this->startDate = $firstLessonDate; 
            	$startDate = new \DateTime($firstLessonDate);
    	        $endDate = $startDate->add(new \DateInterval('P' . $this->weeksCount .'W'));	
        		$this->endDate = $endDate->format('Y-m-d H:i:s');
			} else {
				if(new \DateTime($firstLessonDate) < new \DateTime($secondLessonDate)) {
					$this->startDate = $firstLessonDate; 
				} else {
					$this->startDate = $secondLessonDate;
				}
				$startDate = new \DateTime($secondLessonDate);
				$endDate = $startDate->add(new \DateInterval('P' . $this->weeksCount .'W'));
				$this->endDate = $endDate->format('Y-m-d H:i:s');
			}
        } else {
            $endDate = new \DateTime($this->startDate);
            $startDate = new \DateTime($this->startDate);
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
        if ((int) $this->program->isGroup()) {
			$groupCourse = new CourseGroup();
			$groupCourse->courseId = $this->id;
			$groupCourse->weeksCount = $this->weeksCount;
			$groupCourse->lessonsPerWeekCount = $this->lessonsPerWeekCount;
			$groupCourse->save();
        }
        return parent::afterSave($insert, $changedAttributes);
    }

	public function generateLessons($lessons, $startDate)
	{
		$lessonTime								 = (new \DateTime($this->startDate))->format('H:i:s');
		$duration								 = explode(':', $lessonTime);
		$nextWeekScheduledDate = $startDate;
		$dayList = self::getWeekdaysList();
		$day = $dayList[$this->courseSchedule->day];
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
		$day = $dayList[$this->courseSchedule->day];
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
		$day = $dayList[$this->courseSchedule->day];
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
		$day = $dayList[$this->courseSchedule->day];
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
	public static function groupCourseCount()
	{
		$locationId = Yii::$app->session->get('location_id');
		return self::find()
			->joinWith(['program' => function($query) {
				$query->group()
					->active();
			}])
			->location($locationId)
			->confirmed()
			->count();
	}
	public function getHolidayLessons()
    {
		$lessons = Lesson::findAll(['courseId' => $this->id, 'status' => Lesson::STATUS_DRAFTED]);
		$startDate = (new \DateTime($this->startDate))->format('Y-m-d');
       	$holidays = Holiday::find()
			->andWhere(['>=', 'DATE(date)', $startDate])
            ->all();
		$holidayDates = ArrayHelper::getColumn($holidays, function ($element) {
    		return (new \DateTime($element->date))->format('Y-m-d');
		});
		$lessonIds = [];
		foreach($lessons as $lesson) {
			$lessonDate = (new \DateTime($lesson->date))->format('Y-m-d');
			if(in_array($lessonDate, $holidayDates)) {
				$lessonIds[] = $lesson->id;
}
		}
		return $lessonIds;
	}
	public function createLessons()
	{
		$interval = new \DateInterval('P1D');
		$end = new \DateTime($this->endDate);
		$end->modify('+1 day');
		$lessonsPerWeekCount =  $this->courseGroup->lessonsPerWeekCount; 
		$lessonLimit = ($this->courseGroup->weeksCount * $lessonsPerWeekCount) / $lessonsPerWeekCount;
		for($i = 0; $i < $lessonsPerWeekCount; $i++) {
			$lessonDay = $this->groupCourseSchedule[$i]->day;
			$duration = $this->groupCourseSchedule[$i]->duration; 
			list($hour, $minute, $second) = explode(':', $this->groupCourseSchedule[$i]->fromTime);  
			$start = new \DateTime($this->startDate);
			$start->setTime($hour, $minute, $second);
			$period = new \DatePeriod($start, $interval, $end);
			
			foreach ($period as $day) {
				$checkDay = (int) $day->format('N') === $lessonDay;
				$dayList = self::getWeekdaysList();
				$dayName = $dayList[$lessonDay];
				$lessonCount = Lesson::find()
					->andWhere([
						'courseId' => $this->id,
						'status' => Lesson::STATUS_DRAFTED,
						'DAYNAME(date)' => $dayName,
					])
					->count();
				
				$checkLimit = $lessonCount < $lessonLimit;
				if ($checkDay && $checkLimit) {
					$professionalDevelopmentDay = clone $day;
					$professionalDevelopmentDay->modify('last day of previous month');
					$professionalDevelopmentDay->modify('fifth '.$day->format('l'));
					if ($day->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
						continue;
					}
					$lesson = new Lesson();
					$lesson->setAttributes([
						'courseId' => $this->id,
						'teacherId' => $this->teacherId,
						'status' => Lesson::STATUS_DRAFTED,
						'date' => $day->format('Y-m-d H:i:s'),
						'duration' => $duration,
						'isDeleted' => false,
					]);
					$lesson->save();
				}
			}
		}
	}
}
