<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use common\models\Location;
use yii\validators\Validator;
use common\models\Lesson;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use League\Period\Period;

class StudentValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!empty($model->duration)) {
            $studentId = Yii::$app->filecache->get('studentId' . $model->courseId);
            if ($studentId == false) {
                if ($model->isExtra()) {
                    $studentId = $model->studentId;
                } elseif ($model->course->program->isPrivate()) {
                    $studentId = $model->enrolment->student->id;
                } else {
                    $studentId = !empty($model->studentId) ? $model->studentId : null;
                }
                Yii::$app->filecache->set('studentId' . $model->courseId, $studentId, 60);
            }
            $locationId = Yii::$app->filecache->get('locationId' . $model->courseId);
            if ($locationId == false) {
                $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
                Yii::$app->filecache->set('locationId' . $model->courseId, $locationId, 60);
            }

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
            $studentLessons = Lesson::find()
                ->studentLessons($locationId, $studentId)
                ->andWhere(['NOT', ['lesson.id' => $lessonId]])
                ->isConfirmed()
                ->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
                ->all();
            if ($studentLessons) {
                $this->addError($model, $attribute, 'Lesson time conflicts with student\'s another lesson');
            }
        }
    }
}