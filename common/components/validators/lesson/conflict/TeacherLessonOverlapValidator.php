<?php
namespace common\components\validators\lesson\conflict;

use common\models\Lesson;
use yii\validators\Validator;

class TeacherLessonOverlapValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
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
        if ((!empty($teacherLessons)) && empty($model->vacationId)) {
            $this->addError($model, $attribute, 'Teacher occupied with another lesson');
        }
        if (!empty($model->vacationId) && !empty($teacherLessons)) {
            foreach ($teacherLessons as $teacherLesson) {
                if (new \DateTime($model->date) == new \DateTime($teacherLesson->date) && (int) $teacherLesson->status === Lesson::STATUS_SCHEDULED) {
                    continue;
                }
                $conflictedLessonIds[] = $model->id;
            }
            if (!empty($conflictedLessonIds)) {
                $this->addError($model, $attribute, 'Teacher occupied with another lesson');
            }
        }
    }
}
