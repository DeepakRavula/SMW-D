<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use Yii;
use common\models\Lesson;
use common\models\TeacherAvailability;

class TeacherValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $locationId = Yii::$app->session->get('location_id');
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
			->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
            ->all();
        if ((!empty($teacherLessons)) && empty($model->vacationId)) {
            $this->addError($model,$attribute, 'Teacher occupied with another lesson');
		}
		if(!empty($model->vacationId) && !empty($teacherLessons)) {
			foreach($teacherLessons as $teacherLesson) {
				if(new \DateTime($model->date) == new \DateTime($teacherLesson->date) && (int) $teacherLesson->status === Lesson::STATUS_SCHEDULED) {
					continue;
				}
				$conflictedLessonIds[] = $model->id; 
			}	
			if(!empty($conflictedLessonIds)) {
				$this->addError($model,$attribute, 'Teacher occupied with another lesson');
			}
		}
		$day = (new \DateTime($model->date))->format('N');
        $teacherAvailabilityDay = TeacherAvailability::find()
            ->teacher($model->teacherId)
			->day($day)
            ->all();
		if (empty($teacherAvailabilityDay)) {
            $this->addError($model,$attribute, 'Teacher is not available on '.(new \DateTime($model->date))->format('l'));
        }
		$availableTime = TeacherAvailability::find()
            ->teacher($model->teacherId)
			->day($day)
			->time($lessonStartTime, $lessonEndTime)
            ->all();
		if (empty($availableTime)) {
            $this->addError($model, $attribute, 'Please choose the lesson time within the teacher\'s availability hours');
        }
    }
}
