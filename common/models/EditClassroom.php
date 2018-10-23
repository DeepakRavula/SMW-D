<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\components\validators\lesson\conflict\ClassroomValidator;

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
class EditClassroom extends Model
{
    const SCENARIO_EDIT_CLASSROOM = 'classroom-unavailability';

    public $lessonIds;
    public $lessonId;
    public $classroomId;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['classroomId', 'lessonId'], 'safe'],
            [['classroomId'], ClassroomValidator::className(), 'on' => [self::SCENARIO_EDIT_CLASSROOM]],
        ];
    }
    
    public function ValidateAttribute()
    {
        $lesson = Lesson::findOne($this->lessonId);
        $start = new \DateTime($lesson->date);
        $lessonDate = (new \DateTime($lesson->date))->format('Y-m-d');
        $lessonStartTime = $start->format('H:i:s');
        $duration = (new \DateTime($lesson->duration));
        $start->add(new \DateInterval('PT' . $duration->format('H') . 'H' . $duration->format('i') . 'M'));
        $start->modify('-1 second');
        $lessonEndTime = $start->format('H:i:s');
        $overLapLessons = Lesson::find()
                ->andWhere(['NOT',['lesson.id' => $id]])
                ->andWhere(['classroomId' => $this->classroomId])
                ->isConfirmed()
                ->scheduledOrRescheduled()
                ->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
                ->all();
        if ($overLapLessons) {
            $this->addError($model, $attribute, 'Classroom already chosen!');
        }
        $lesson = Lesson::findOne($this->lessonId);
        $lessonDate = (new \DateTime($lesson->date))->format('Y-m-d');
        $classroomUnavailabilities = ClassroomUnavailability::find()
                ->andWhere(['classroomId' => $classroomId])
                ->andWhere(['AND',
                        ['<=', 'DATE(fromDate)', $lessonDate],
                        ['>=', 'DATE(toDate)', $lessonDate]
                ])
                ->all();
        if ($classroomUnavailabilities) {
            $this->addError($model, $attribute, 'Classroom is unavailable');
        }
    }
}
