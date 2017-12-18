<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;
use common\models\User;
use Yii;
use common\models\Lesson;
use common\models\TeacherAvailability;

class TeacherSubstituteValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
        if (!in_array($model->teacherId, ArrayHelper::getColumn(User::find()
            ->teachers($model->course->programId, $locationId)->notDeleted()->all(), 'id'))) {
            $this->addError($model, $attribute, '<p style = "background-color: red">Teacher unqualified</p>');
        }
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
            $this->addError($model,$attribute, '<p style = "background-color: red">Teacher occupied with another lesson</p>');
        }
        if(!empty($model->vacationId) && !empty($teacherLessons)) {
            foreach($teacherLessons as $teacherLesson) {
                if(new \DateTime($model->date) == new \DateTime($teacherLesson->date) && (int) $teacherLesson->status === Lesson::STATUS_SCHEDULED) {
                    continue;
                }
                $conflictedLessonIds[] = $model->id; 
            }	
            if(!empty($conflictedLessonIds)) {
                $this->addError($model,$attribute, '<p style = "background-color: red">Teacher occupied with another lesson</p>');
            }
        }
        $day = (new \DateTime($model->date))->format('N');
        $teacherAvailabilityDay = TeacherAvailability::find()
            ->teacher($model->teacherId)
            ->day($day)
            ->all();
        
        if (empty($teacherAvailabilityDay)) {
            $this->addError($model,$attribute, Lesson::TEACHER_UNSCHEDULED_ERROR_MESSAGE);
        }
        $availableTime = TeacherAvailability::find()
            ->teacher($model->teacherId)
            ->day($day)
            ->time($lessonStartTime, $lessonEndTime)
            ->all();
        if (empty($availableTime)) {
            $this->addError($model, $attribute, Lesson::TEACHER_UNSCHEDULED_ERROR_MESSAGE);
        }
    }
}
