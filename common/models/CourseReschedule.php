<?php

namespace common\models;
use Carbon\Carbon;
use Carbon\CarbonInterval;

use yii\base\Model;
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
class CourseReschedule extends Model
{
    public $fromDate;
    public $toDate;
    public $dateToChangeSchedule;
    public $rescheduleBeginDate;
    public $duration;
    public $dayTime;
    public $courseId;
    public $teacherId;


    const SCENARIO_BASIC = 'reschedule-basic';
    const SCENARIO_DETAILED = 'reschedule-detail';

    public function setModel($model)
    {
        $this->duration = $model->duration;
        $this->teacherId = $model->teacherId;
        $this->dayTime = (new \DateTime($model->startDate))->format('l h:i A');
        $this->courseId = $model->id;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dayTime', 'teacherId', 'duration', 'rescheduleBeginDate'], 'required', 'on' => self::SCENARIO_DETAILED],
            [['dateToChangeSchedule'], 'required', 'on' => self::SCENARIO_BASIC],
            ['dateToChangeSchedule', 'validateDate', 'on' => self::SCENARIO_BASIC],
            [['dayTime', 'teacherId', 'duration'], 'safe'],
            [['dateToChangeSchedule', 'rescheduleBeginDate'], 'safe'],
            [['courseId'], 'safe'],
            ['dateToChangeSchedule', 'validateDateToChangeSchedule'],
            ['rescheduleBeginDate','validateRescheduleBeginDate']
            
        ];
    }

    public function validateDate($attributes)
    {
        $lessons = Lesson::find()
            ->andWhere(['courseId' => $this->courseId])
            ->regular()
            ->notDeleted()
            ->statusScheduled()
            ->isConfirmed()
            ->andWhere(['>=', 'DATE(lesson.date)', (new \DateTime($this->dateToChangeSchedule))->format('Y-m-d')])
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        if (!$lessons) {
            $this->addError($attributes, "There were no lessons to change schedule!");
        }
    }
    
    

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'duration' => 'Duration',
            'teacherId' => 'Teacher',
            'rescheduleBeginDate' => 'Reschedule begin',
            'dateToChangeSchedule' => 'Date to change schedule',
            'dayTime' => 'Day & Time'
        ];
    }

    public function reschdeule()
    {
        Lesson::deleteAll([
            'courseId' => $this->courseId,
            'isConfirmed' => false,
        ]);
        $lessonIds = [];
        $rescheduleStartDate = (new \DateTime($this->rescheduleBeginDate))->modify('-1 day');
        $rescheduledLessons = Lesson::find()
            ->andWhere(['courseId' => $this->courseId])
            ->regular()
            ->notDeleted()
            ->rescheduled()
            ->notCanceled()
            ->isConfirmed()
            ->andWhere(['>=', 'DATE(lesson.date)', (new \DateTime($this->dateToChangeSchedule))->format('Y-m-d')])
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        $dateToChangeSchedule = (new \DateTime($this->dateToChangeSchedule))->format('Y-m-d H:i:s');
        foreach ($rescheduledLessons as $rescheduledLesson) {
            if ($rescheduledLesson->getOriginalDate() < $dateToChangeSchedule) {
                $lessonIds[] = $rescheduledLesson->id;
            }
        }
        $lessons = Lesson::find()
            ->andWhere(['courseId' => $this->courseId])
            ->regular()
            ->notDeleted()
            ->notCanceled() 
            ->isConfirmed()
            ->andWhere(['NOT', ['lesson.id' => $lessonIds]])
            ->andWhere(['>=', 'DATE(lesson.date)', (new \DateTime($this->dateToChangeSchedule))->format('Y-m-d')])
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        $course = Course::findOne($this->courseId);
        $dayList = Course::getWeekdaysList();
        $day = $dayList[(new \DateTime($this->dayTime))->format('N')];
        $rescheduleStartDate->modify('next ' . $day);
        $lastLessonDate = $course->generateLessons($lessons, $rescheduleStartDate, $this->teacherId, $this->dayTime, $this->duration);
        return $lastLessonDate;
    }

    public function validateDateToChangeSchedule($attribute) {
        $date = Carbon::parse($this->dateToChangeSchedule);
        $first = $date->modify('first day of this month');
        $firstday = Carbon::parse($first)->format('Y-m-d');
        $last = $date->modify('last day of this month');
        $lastday = Carbon::parse($last)->format('Y-m-d');
        $lesson = Lesson::find()
            ->andWhere(['courseId' => $this->courseId])
            ->regular()
            ->notDeleted()
            ->statusScheduled()
            ->isConfirmed()
            ->invoiced()
            ->andWhere(['between', 'DATE(lesson.date)', $firstday, $lastday])
            ->orderBy(['lesson.date' => SORT_ASC])
            ->one();
         if (!empty($lesson)){
             $this->addError($attribute, "You cannot select this month");
         }
    }
    
    public function validateRescheduleBeginDate($attribute) {
        $ddate = ($this->rescheduleBeginDate);
        $givenday = carbon::parse($ddate)->format('Y-m-d');
        $date = Carbon::parse($ddate);
        $first = Carbon::parse($date->modify('first day of this month'));
        $firstDate = Clone $first;
        $endOfWeek = $firstDate->modify('+6 days')->format('Y-m-d');
        if ($givenday > $endOfWeek) {
            $this->addError($attribute, "Reschedule begin date should be within first seven days (".Carbon::parse($first)->format('M d, Y')." - ".Carbon::parse($endOfWeek)->format('M d, Y').")of the month");
        }
    }
}
