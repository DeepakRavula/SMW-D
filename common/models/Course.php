<?php

namespace common\models;

use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Lesson;
use Yii;
use Carbon\Carbon;
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
 * @property string $startDate
 * @property string $endDate
 */
class Course extends \yii\db\ActiveRecord
{
    const SCENARIO_GROUP_COURSE = 'group-course';
    const SCENARIO_EDIT_ENROLMENT = 'edit-enrolment';
    const EVENT_CREATE = 'event-create';
    const SCENARIO_EXTRA_GROUP_COURSE = 'extra-group-course';

    const TYPE_REGULAR = 1;
    const TYPE_EXTRA = 2;

    public $lessonStatus;
    public $rescheduleBeginDate;
    public $weeksCount;
    public $lessonsPerWeekCount;
    public $userName;
    public $studentId;
    public $duration;

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
            [['weeksCount'], 'required', 'when' => function ($model, $attribute) {
                return (int)$model->program->type === Program::TYPE_GROUP_PROGRAM;
            }, 'except' => self::SCENARIO_EXTRA_GROUP_COURSE],
            [['startDate'], 'required', 'except' => self::SCENARIO_GROUP_COURSE],
            [['startDate', 'endDate'], 'safe'],
            [['startDate', 'endDate'], 'safe', 'on' => self::SCENARIO_GROUP_COURSE],
            [['programId', 'teacherId', 'weeksCount', 'lessonsPerWeekCount'], 'integer'],
            [['locationId', 'rescheduleBeginDate', 'isConfirmed', 'studentId','duration'], 'safe'],
          
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'programId' => 'Program',
            'teacherId' => 'Teacher',
            'locationId' => 'Location',
            'day' => 'Day',
            'fromTime' => 'From Time',
            'duration' => 'Duration',
            'startDate' => 'Start Date',
            'endDate' => 'End Date',
            'paymentFrequency' => 'Payment Frequency',
            'rescheduleBeginDate' => 'Reschedule Future Lessons From',
            'rescheduleFromDate' => 'With effects from',
            'showAllCourses' => 'Show All'
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
            'Sunday'
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

    public function getCourseSchedule()
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
    
    public function getExtraLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'id'])
                ->onCondition(['lesson.type' => Lesson::TYPE_EXTRA]);
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
        if (!$insert) {
            return parent::beforeSave($insert);
        }
        if (empty($this->isConfirmed)) {
            $this->isConfirmed = false;
        }
        if (empty($this->type)) {
            $this->type = self::TYPE_REGULAR;
        }
        if ((int) $this->program->isGroup() && !$this->isExtra()) {
            $startDate = new \DateTime($this->startDate);
            $this->startDate = (new \DateTime($this->startDate))->format('Y-m-d H:i:s');
            $weeks = $this->weeksCount;
            $endDate = $startDate->add(new \DateInterval('P' . $weeks .'W'));
            $this->endDate = $endDate->format('Y-m-d H:i:s');
        } else {
            $endDate = (new Carbon($this->startDate))->addMonths(11);
            $startDate = new \DateTime($this->startDate);
            $this->startDate = $startDate->format('Y-m-d H:i:s');
            $this->endDate = $endDate->endOfMonth();
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            return parent::afterSave($insert, $changedAttributes);
        }
        if ((int) $this->program->isGroup() && !$this->isExtra()) {
            $groupCourse = new CourseGroup();
            $groupCourse->courseId = $this->id;
            $groupCourse->weeksCount = $this->weeksCount;
            $groupCourse->lessonsPerWeekCount = $this->lessonsPerWeekCount;
            $groupCourse->save();
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function generateLessons($lessons, $startDate, $teacherId)
    {
        $lessonTime	= (new \DateTime($this->startDate))->format('H:i:s');
        list($hour, $minute, $second) = explode(':', $lessonTime);
        $nextWeekScheduledDate = $startDate;
        $dayList = self::getWeekdaysList();
        $day = $dayList[$startDate->format('N')];
        foreach ($lessons as $lesson) {
            $lesson->id			 = null;
            $lesson->isNewRecord = true;
            $lesson->teacherId = $teacherId;
            $lesson->status		 = Lesson::STATUS_SCHEDULED;
            $nextWeekScheduledDate->setTime($hour, $minute, $second);
            $lesson->date		 = $nextWeekScheduledDate->format('Y-m-d H:i:s');
            $lesson->isConfirmed = false;
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

    public static function groupCourseCount()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        return self::find()
            ->joinWith(['program' => function ($query) {
                $query->group()
                    ->active();
            }])
            ->location($locationId)
            ->confirmed()
            ->count();
    }
    
    public function getHolidayLessons()
    {
        $lessons = Lesson::findAll(['courseId' => $this->id, 'isConfirmed' => false]);
        $startDate = (new \DateTime($this->startDate))->format('Y-m-d');
        $holidays = Holiday::find()
            ->andWhere(['>=', 'DATE(date)', $startDate])
            ->all();
        $holidayDates = ArrayHelper::getColumn($holidays, function ($element) {
            return (new \DateTime($element->date))->format('Y-m-d');
        });
        $lessonIds = [];
        foreach ($lessons as $lesson) {
            $lessonDate = (new \DateTime($lesson->date))->format('Y-m-d');
            if (in_array($lessonDate, $holidayDates)) {
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
        for ($i = 0; $i < $lessonsPerWeekCount; $i++) {
            $lessonDay = $this->groupCourseSchedule[$i]->day;
            $time = $this->groupCourseSchedule[$i]->fromTime;
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
                        'DAYNAME(date)' => $dayName,
                        'TIME(date)' => $time
                    ])
                    ->scheduled()
                    ->count();
                
                $checkLimit = $lessonCount < $lessonLimit;
                if ($checkDay && $checkLimit) {
                    $this->createLesson($day);
                }
            }
        }
    }
    
    public function createLesson($day, $isConfirmed = null)
    {
        if (!$isConfirmed) {
            $isConfirmed = false;
        }
        $lesson = new Lesson();
        if ($day < new \DateTime()) {
            $status = Lesson::STATUS_UNSCHEDULED;
        } else {
            $status = Lesson::STATUS_SCHEDULED;
        }
        $lesson->setAttributes([
            'courseId' => $this->id,
            'teacherId' => $this->teacherId,
            'status' => $status,
            'date' => $day->format('Y-m-d H:i:s'),
            'duration' => $this->courseSchedule->duration,
            'isConfirmed' => $isConfirmed,
        ]);
        $lesson->save();
    }

    public function createExtraLessonEnrolment()
    {
        $enrolment                     = new Enrolment();
        $enrolment->courseId           = $this->id;
        $enrolment->studentId          = $this->studentId;
        $enrolment->isConfirmed        = true;
        $enrolment->paymentFrequencyId = false;
        $enrolment->save();
        return $enrolment;
    }
    
    public function checkExtraCourseExist()
    {
        $enroledCourse = $this->getExtraCourse();
        return !empty($enroledCourse);
    }
    
    public function getExtraCourse()
    {
        $programId = $this->programId;
        $studentId = $this->studentId;
        $course = self::find()
                ->confirmed()
                ->extra()
                ->joinWith(['enrolment' => function ($query) use ($studentId) {
                    $query->andWhere(['enrolment.studentId' => $studentId]);
                }])
                ->andWhere(['course.programId' => $programId])
                ->one();
        return $course ?? null;
    }
    
    public function hasExtraLesson()
    {
        return Lesson::find()
                ->andWhere(['courseId' => $this->id])
                ->extra()
                ->exists();
    }
    
    public function isExtra()
    {
        return (int) $this->type === (int) self::TYPE_EXTRA;
    }
}
