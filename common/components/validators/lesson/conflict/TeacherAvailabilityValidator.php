<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use common\models\TeacherAvailability;

class TeacherAvailabilityValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
        $lessonDuration = explode(':', $model->fullDuration);
        $date = new \DateTime($model->date);
        $date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));
        $date->modify('-1 second');
        $lessonEndTime = $date->format('H:i:s');
        $day = (new \DateTime($model->date))->format('N');
        $teacher = TeacherAvailability::find()
                        ->teacher($model->teacherId)
            ->day($day);
        $teacherAvailabilityDay = $teacher->all();
        if (empty($teacherAvailabilityDay)) {
            $this->addError($model, $attribute, 'Teacher is not available on '.(new \DateTime($model->date))->format('l'));
        }
        $availableTime = $teacher->time($lessonStartTime, $lessonEndTime)
                        ->all();
        if (empty($availableTime)) {
            $this->addError($model, $attribute, 'Please choose the lesson time within the teacher\'s availability hours');
        }
    }
}
