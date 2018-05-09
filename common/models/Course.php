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

    public $programRate;
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
            [['startDate', 'endDate', 'programRate'], 'safe'],
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

    public function setModel($model)
    {
        $this->programId = $model->programId;
        $date = new \DateTime($model->startDate);
        $duration = new \DateTime($model->fromTime);
        $date->add(new \DateInterval('PT' . $duration->format('H') . 'H' . $duration->format('i') . 'M'));
        $this->startDate = $date->format('Y-m-d H:i:s');
        $this->teacherId = $model->teacherId;
        $this->programRate = $model->programRate;
        return $this;
    }
    
    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacherId']);
    }

    public function isPrivate()
    {
        return (int) $this->program->type === (int) Program::TYPE_PRIVATE_PROGRAM;
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
    
    public function getCourseProgramRate()
    {
        return $this->hasOne(CourseProgramRate::className(), ['courseId' => 'id']);
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
    
    public function getRegularCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'courseId'])
                ->viaTable('course_extra', ['extraCourseId' => 'id']);
    }
    
    public function getExtraCourses()
    {
        return $this->hasMany(Course::className(), ['id' => 'extraCourseId'])
                ->viaTable('course_extra', ['courseId' => 'id']);
    }
    
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'locationId']);
    }

    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'id'])
                ->onCondition(['lesson.isDeleted' => false, 'lesson.isConfirmed' => true,
                    'lesson.status' => [Lesson::STATUS_RESCHEDULED, Lesson::STATUS_SCHEDULED,
                        Lesson::STATUS_UNSCHEDULED]]);
    }
    
    public function getExtraLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'id'])
                ->onCondition(['lesson.isDeleted' => false, 'lesson.isConfirmed' => true,
                    'lesson.status' => [Lesson::STATUS_RESCHEDULED, Lesson::STATUS_SCHEDULED,
                        Lesson::STATUS_UNSCHEDULED], 'lesson.type' => Lesson::TYPE_EXTRA]);
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
        if (empty($this->locationId)) {
            $this->locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
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
            $endDate = (new Carbon($this->startDate))->addMonths(23);
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
        } else {
            $courseProgramRate = new CourseProgramRate();
            $courseProgramRate->courseId = $this->id;
            $courseProgramRate->startDate  = (new Carbon($this->startDate))->format('Y-m-d');
            $courseProgramRate->endDate = (new Carbon($this->endDate))->format('Y-m-d');
            $courseProgramRate->programRate = $this->programRate ? $this->programRate : $this->program->rate;
            $courseProgramRate->applyFullDiscount = false;
            $courseProgramRate->save();
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

    public function generateLessons($lessons, $startDate, $teacherId, $dayTime)
    {
        $hour = (new \DateTime($dayTime))->format('H');
        $minute = (new \DateTime($dayTime))->format('i');
        $second = (new \DateTime($dayTime))->format('s');
        $dayList = self::getWeekdaysList();
        $day = $dayList[(new \DateTime($dayTime))->format('N')];
        $nextWeekScheduledDate = $startDate;
        foreach ($lessons as $lesson) {
            $lesson->id = null;
            $lesson->isNewRecord = true;
            $lesson->teacherId = $teacherId;
            $lesson->status = Lesson::STATUS_SCHEDULED;
            $nextWeekScheduledDate->setTime($hour, $minute, $second);
            $lesson->date = $nextWeekScheduledDate->format('Y-m-d H:i:s');
            $lesson->isConfirmed = false;
            if ($this->isProfessionalDevelopmentDay($nextWeekScheduledDate)) {
                $startDate->modify('next ' . $day);
                $nextWeekScheduledDate->setTime($hour, $minute, $second);
                $lesson->date = $nextWeekScheduledDate->format('Y-m-d H:i:s');
            }
            $lesson->save();
            $startDate->modify('next ' . $day);
        }
        return $lesson->date;
    }

    public function isProfessionalDevelopmentDay($startDate)
    {
        $dayList = self::getWeekdaysList();
        $day = $dayList[$startDate->format('N')];
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
        $status = Lesson::STATUS_SCHEDULED;
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
        $enrolment->paymentFrequencyId = 0;
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

    public function hasRegularCourse()
    {
        return !empty($this->getStudentRegularCourse());
    }

    public function getStudentRegularCourse()
    {
        $programId = $this->programId;
        $studentId = $this->studentId;
        $course = self::find()
                ->confirmed()
                ->regular()
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

    public function isRegular()
    {
        return (int) $this->type === (int) self::TYPE_REGULAR;
    }
    
    public function extendTo($course)
    {
        $courseExtend = new CourseExtra();
        $courseExtend->courseId = $this->id;
        $courseExtend->extraCourseId = $course->id;
        return $courseExtend->save();
    }
    
    public function hasExtraCourse()
    {
        return !empty($this->extraCourses);
    }
}
