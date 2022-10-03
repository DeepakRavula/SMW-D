<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use Yii;
use common\models\Lesson;

class StudentAvailabilityValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $hasCookie = Yii::$app->getRequest()->getCookies()->has('locationId');
        if($hasCookie){
           $locationId = Yii::$app->getRequest()->getCookies()->getValue('locationId');
        }
        $studentId = $model->course->enrolment->student->id;
        $start               = new \DateTime($model->date);
        $lessonDate = (new \DateTime($model->date))->format('Y-m-d');
        $lessonStartTime = $start->format('H:i:s');
        $duration = $model->newDuration->format('H:i:s');
        $lessonDuration = explode(':', $duration);
        $start->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));
        $start->modify('-1 second');
        $lessonEndTime = $start->format('H:i:s');
        $lessonId = [$model->id];
        if ($model->lessonId) {
            $lessonId = $model->lessonId;
            array_push($lessonId, $model->id);
        }
        $studentLessons = Lesson::find()
            ->studentLessons($locationId, $studentId)
            ->andWhere(['NOT', ['lesson.id' => $lessonId]])
            ->isConfirmed()
            ->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
            ->all();
        if ((!empty($studentLessons))) {
            $this->addError($model, $attribute, 'Lesson time conflicts with student\'s another lesson');
        }
    }
}
