<?php

namespace common\models;

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
    public $dateRangeToChangeSchedule;
    public $rescheduleBeginDate;
    public $duration;
    public $dayTime;
    public $courseId;
    public $teacherId;


    const SCENARIO_BASIC = 'reschedule-basic';
    const SCENARIO_DETAILED = 'reschedule-detail';

    public function setDateRangeToChangeSchedule($dateRangeToChangeSchedule)
    {
        list($fromDate, $toDate) = explode(' - ', $dateRangeToChangeSchedule);
        $this->fromDate = \DateTime::createFromFormat('M d,Y', $fromDate);
        $this->toDate = \DateTime::createFromFormat('M d,Y', $toDate);
    }

    public function setModel($model)
    {
        $this->duration = $model->duration;
        $this->teacherId = $model->teacherId;
        $this->dayTime = (new \DateTime($model->startDate))->format('l h:i A');
        $this->courseId = $model->id;
        return $this;
    }

    public function getDateRangeToChangeSchedule()
    {
        $fromDate = $this->fromDate->format('M d,Y');
        $toDate = $this->toDate->format('M d,Y');
        $this->dateRangeToChangeSchedule = $fromDate.' - '.$toDate;

        return $this->dateRangeToChangeSchedule;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dayTime', 'teacherId', 'duration'], 'required', 'on' => self::SCENARIO_DETAILED],
            [['dateRangeToChangeSchedule', 'rescheduleBeginDate'], 'required', 'on' => self::SCENARIO_BASIC],
            [['dayTime', 'teacherId', 'duration', 'dateRangeToChangeSchedule',
                'rescheduleBeginDate'], 'required', 'except' => [self::SCENARIO_BASIC, self::SCENARIO_DETAILED]],
            [['dayTime', 'teacherId', 'duration'], 'safe', 'on' => self::SCENARIO_BASIC],
            [['dateRangeToChangeSchedule', 'rescheduleBeginDate'], 'safe', 'on' => self::SCENARIO_DETAILED],
            [['courseId'], 'safe']
        ];
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
            'dateRangeToChangeSchedule' => 'Date range',
            'dayTime' => 'Day & Time'
        ];
    }

    public function reschdeule()
    {
        Lesson::deleteAll([
            'courseId' => $this->courseId,
            'isConfirmed' => false,
        ]);
        list($fromDate, $toDate) = explode(' - ', $this->dateRangeToChangeSchedule);
        $startDate = new \DateTime($fromDate);
        $endDate = new \DateTime($toDate);
        $rescheduleStartDate = (new \DateTime($this->rescheduleBeginDate))->modify('-1 day');
        $lessons = Lesson::find()
            ->andWhere(['courseId' => $this->courseId])
            ->regular()
            ->notDeleted()
            ->statusScheduled()
            ->isConfirmed()
            ->between($startDate, $endDate)
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        $course = Course::findOne($this->courseId);
        $dayList = Course::getWeekdaysList();
        $day = $dayList[(new \DateTime($this->dayTime))->format('N')];
        $rescheduleStartDate->modify('next ' . $day);
        $rescheduleLastdate = $course->generateLessons($lessons, $rescheduleStartDate, $this->teacherId, $this->dayTime);
        $rescheduleBegindate = $rescheduleStartDate->format('M d,Y');
        $rescheduleEnddate = (new \DateTime($rescheduleLastdate))->format('M d,Y');
        return $rescheduleBegindate . ' - ' . $rescheduleEnddate;
    }
}
