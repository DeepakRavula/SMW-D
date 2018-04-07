<?php

namespace common\models;

use common\components\validators\reschedule\CourseRescheduleBeginValidator;
use common\components\validators\reschedule\CourseRescheduleEndValidator;

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
class CourseReschedule extends Course
{
    public $rescheduleBeginDate;
    public $rescheduleEndDate;
    public $duration;
    public $dayTime;
    public $courseId;
    public $teacherId;

    public function setModel($model)
    {
        $this->duration = $model->duration;
        $this->teacherId = $model->teacherId;
        $this->courseId = $model->id;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dayTime', 'teacherId', 'duration', 'rescheduleEndDate', 'rescheduleBeginDate'], 'required'],
            [['courseId'], 'safe'],
            [['rescheduleBeginDate'], CourseRescheduleBeginValidator::className()],
            [['rescheduleEndDate'], CourseRescheduleEndValidator::className()]
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
            'rescheduleBeginDate' => 'Reschedule start',
            'rescheduleEndDate' => 'Reschedule End',
            'dayTime' => 'Day & Time'
        ];
    }

    public function reschdeule()
    {
        Lesson::deleteAll([
            'courseId' => $this->courseId,
            'isConfirmed' => false,
        ]);
        $endDate = new \DateTime($this->rescheduleEndDate);
        $startDate = new \DateTime($this->rescheduleBeginDate);
        $lessons = Lesson::find()
            ->andWhere(['courseId' => $this->courseId])
            ->regular()
            ->notDeleted()
            ->scheduled()
            ->isConfirmed()
            ->between($startDate, $endDate)
            ->all();
        $course = Course::findOne($this->courseId);
        $dayList = self::getWeekdaysList();
        $day = $dayList[(new \DateTime($this->dayTime))->format('N')];
        $startDate->modify('next ' . $day);
        $course->generateLessons($lessons, $startDate, $this->teacherId, $this->dayTime);
        return true;
    }
}
