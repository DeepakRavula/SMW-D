<?php
namespace common\components\validators\lesson\conflict;

use common\models\Lesson;
use common\models\Location;
use yii\validators\Validator;

class TeacherLessonOverlapValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if ($model->duration) {
            $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
            $lessonDate = (new \DateTime($model->date))->format('Y-m-d');
            $lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
            $lessonDuration = explode(':', $model->fullDuration);
            $date = new \DateTime($model->date);
            $date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));
            $date->modify('-1 second');
            $lessonEndTime = $date->format('H:i:s');
            $teacherLessons = Lesson::find()
                ->teacherLessons($locationId, $model->teacherId)
                ->andWhere(['NOT', ['lesson.id' => $model->id]])
                ->isConfirmed()
                ->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
                ->all();
            if ($teacherLessons) {
                $this->addError($model, $attribute, 'Teacher occupied with another lesson');
            }
        }
    }
}
