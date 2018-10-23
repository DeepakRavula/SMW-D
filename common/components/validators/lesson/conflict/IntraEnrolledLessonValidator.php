<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use common\models\Lesson;

class IntraEnrolledLessonValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $lessonDate = (new \DateTime($model->date))->format('Y-m-d');
        $lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
        $lessonDuration = explode(':', $model->duration);
        $date = new \DateTime($model->date);
        $date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));
        $date->modify('-1 second');
        $lessonEndTime = $date->format('H:i:s');
        $lessonId = [$model->id];
        if ($model->lessonId) {
            $lessonId = $model->lessonId;
            array_push($lessonId, $model->id);
        }
        $draftLessons = Lesson::find()
            ->andWhere(['courseId' => $model->courseId, 'isConfirmed' => false])
            ->andWhere(['NOT', ['id' => $lessonId]])
            ->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
            ->all();
        
        if (!empty($draftLessons)) {
            $this->addError($model, $attribute, 'Lesson time conflicts with same enrolment lesson');
        }
    }
}
