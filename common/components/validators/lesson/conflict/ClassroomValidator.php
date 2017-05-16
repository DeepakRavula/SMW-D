<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use Yii;
use common\models\Lesson;

class ClassroomValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $start = new \DateTime($model->date);
        $lessonDate = (new \DateTime($model->date))->format('Y-m-d');
        $lessonStartTime = $start->format('H:i:s');
        $duration = (new \DateTime($model->duration));
        $start->add(new \DateInterval('PT' . $duration->format('H') . 'H' . $duration->format('i') . 'M'));
        $start->modify('-1 second');
        $lessonEndTime = $start->format('H:i:s');
        $overLapLessons = Lesson::find()
                ->andWhere(['classroomId' => $model->classroomId])
                ->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
                ->all();
        if (!empty($overLapLessons)) {
            $this->addError($model, $attribute, 'Classroom already chosen!');
        }
    }
}
