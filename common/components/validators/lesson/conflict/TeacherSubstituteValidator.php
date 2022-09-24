<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Location;
use common\models\Lesson;
use common\models\TeacherAvailability;

class TeacherSubstituteValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        // $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $hasCookie = Yii::$app->getRequest()->getCookies()->has('locationId');
        if($hasCookie){
           $locationId = Yii::$app->getRequest()->getCookies()->getValue('locationId');
        }
        if (!in_array($model->teacherId, ArrayHelper::getColumn(User::find()
            ->teachers($model->course->programId, $locationId)->notDeleted()->all(), 'id'))) {
            $this->addError($model, $attribute, '<p style = "background-color: red">Teacher unqualified</p>');
        }
        $lessonDate = (new \DateTime($model->date))->format('Y-m-d');
        $lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
        $lessonDuration = explode(':', $model->duration);
        $date = new \DateTime($model->date);
        $date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));
        $date->modify('-1 second');
        $lessonEndTime = $date->format('H:i:s');
        $teacherLessons = Lesson::find()
            ->teacherLessons($locationId, $model->teacherId)
            ->andWhere(['NOT', ['lesson.id' => $model->id]])
            ->isConfirmed()
            ->present()
            ->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
            ->all();
        if ($teacherLessons) {
            $this->addError($model, $attribute, '<p style = "background-color: red">Teacher occupied with another lesson</p>');
        }
        $day = (new \DateTime($model->date))->format('N');
        $teacherAvailabilityDay = TeacherAvailability::find()
            ->teacher($model->teacherId)
            ->day($day)
			->notDeleted()
            ->all();
        
        if (empty($teacherAvailabilityDay)) {
            $this->addError($model, $attribute, Lesson::TEACHER_UNSCHEDULED_ERROR_MESSAGE);
        }
        $availableTime = TeacherAvailability::find()
            ->teacher($model->teacherId)
            ->day($day)
            ->time($lessonStartTime, $lessonEndTime)
			->notDeleted()
            ->all();
        if (empty($availableTime)) {
            $this->addError($model, $attribute, Lesson::TEACHER_UNSCHEDULED_ERROR_MESSAGE);
        }
    }
}
