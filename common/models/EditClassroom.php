<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\Lesson;
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
    const SCENARIO_EDIT_CLASSROOM = 'classroom-edit';
    const SCENARIO_BEFORE_EDIT_CLASSROOM = 'before-classroom-edit';

    public $lessonIds;
    public $classroomId;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['classroomId', 'lessonIds'], 'safe'],
            [['lessonIds'], 'validateOnInvoiced', 'on' => [self::SCENARIO_BEFORE_EDIT_CLASSROOM]],
            [['lessonIds'], 'validateClassRoom', 'on' => [self::SCENARIO_EDIT_CLASSROOM]],
            [['classroomId'], 'validateClassRoomAvailability', 'on' => [self::SCENARIO_EDIT_CLASSROOM]],
        ];
    }
    public function validateOnInvoiced($attribute)
    {
       foreach($this->lessonIds as $lessonId) {
          $lesson = Lesson::findOne($lessonId);
          if ($lesson->hasInvoice()) {
            $this->addError($attribute, "One of the selected lessons is invoiced. Invoiced lessons can't be edited.");
        }
      }
    }
    public function validateClassRoom($attribute)
    {
        foreach($this->lessonIds as $lessonId) { 
            $lesson = Lesson::findOne($lessonId);
            $start = new \DateTime($lesson->date);
            $lessonDate = (new \DateTime($lesson->date))->format('Y-m-d');
            $lessonStartTime = $start->format('H:i:s');
            $duration = (new \DateTime($lesson->duration));
            $start->add(new \DateInterval('PT' . $duration->format('H') . 'H' . $duration->format('i') . 'M'));
            $start->modify('-1 second');
            $lessonEndTime = $start->format('H:i:s');
            $overLapLessons = Lesson::find()
                    ->andWhere(['NOT',['lesson.id' => $lessonId]])
                    ->andWhere(['classroomId' => $this->classroomId])
                    ->isConfirmed()
                    ->scheduledOrRescheduled()
                    ->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
                    ->all();
        if ($overLapLessons) {
            $this->addError($attribute, 'Classroom already chosen!');
        }
    }
}
public function validateClassRoomAvailability($attribute)
{
    foreach($this->lessonIds as $lessonId) {
        $lesson = Lesson::findOne($lessonId);
        $lessonDate = (new \DateTime($lesson->date))->format('Y-m-d');
        $classroomUnavailabilities = ClassroomUnavailability::find()
                ->andWhere(['classroomId' => $this->classroomId])
                ->andWhere(['AND',
                        ['<=', 'DATE(fromDate)', $lessonDate],
                        ['>=', 'DATE(toDate)', $lessonDate]
                ])
                ->all();
        if ($classroomUnavailabilities) {
            $this->addError($attribute, 'Classroom is unavailable');
        }
    }
}
}
