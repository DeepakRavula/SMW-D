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
            [['courseId'], 'safe']
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
        $currentDate = new \DateTime();
        $current_date = $currentDate->format('Y-m-d H:i:s');
        foreach ($rescheduledLessons as $rescheduledLesson) {
            if ($rescheduledLesson->parent()->one()->date < $current_date) {
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
}
