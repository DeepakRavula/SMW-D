<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use common\models\Lesson;

class StudentBackToBackLessonValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $lessonDate = (new \DateTime($model->date))->format('Y-m-d');
        $lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
        $lessonDuration = explode(':', $model->fullDuration);
        $date = new \DateTime($model->date);
        $date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));
        $lessonFullEndTime = $date->format('H:i:s');
        $date->modify('-1 second');
        $query = Lesson::find()
                    ->andWhere(['NOT', ['lesson.id' => $model->id]]);
        if(!empty($model->enrolment->id)) {
            $studentBackToBackLessons = $query->enrolment($model->enrolment->id)
                    ->backToBackOverlap($lessonDate, $lessonStartTime, $lessonFullEndTime)
                    ->all();
            if(!empty($studentBackToBackLessons)) {
                foreach($studentBackToBackLessons as $studentBackToBackLesson) {
                    if(new \DateTime($model->date) == new \DateTime($studentBackToBackLesson->date) && (int) $studentBackToBackLesson->status === Lesson::STATUS_SCHEDULED) {
                        continue;
                    }
                    $conflictedLessons[] = $model->id;
                }
                if(!empty($conflictedLessons)) {
                    $this->addError($model,$attribute, 'Lesson cannot be scheduled '
                        . 'back to back with same enrolment\'s another lesson');
                }
            }
        }
    }
}
