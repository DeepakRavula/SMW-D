<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use common\models\Lesson;
use common\models\Location;
use yii\validators\Validator;

class TeacherLessonOverlapValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        
        if ($model->duration) {
            $locationId = Yii::$app->filecache->get('locationId');
            if($locationId == false)
            {
                $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
                Yii::$app->cache->set('locationId',$locationId, 60);
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
            $teacherLessons = Lesson::find()
                ->teacherLessons($locationId, $model->teacherId)
                ->andWhere(['NOT', ['in', 'lesson.id', $lessonId]])
                ->isConfirmed()
                ->present()
                ->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
                ->all();
            if ($teacherLessons) {
                $this->addError($model, $attribute, 'Teacher occupied with another lesson');
            }
        }
    }
}
