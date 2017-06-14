<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use common\models\Lesson;

class StudentValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
		if($model->course->program->isPrivate()) {
			$studentId = $model->course->enrolment->student->id; 
		} else {
			$studentId = !empty($model->studentId) ? $model->studentId : null;
		}
       	$locationId = Yii::$app->session->get('location_id');
		$lessonDate = (new \DateTime($model->date))->format('Y-m-d');
		$lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
		$lessonDuration = explode(':', $model->fullDuration);
		$date = new \DateTime($model->date);
		$date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));	
		$date->modify('-1 second');
		$lessonEndTime = $date->format('H:i:s');
		$studentLessons = Lesson::find()
			->studentLessons($locationId, $studentId)
			->andWhere(['NOT', ['lesson.id' => $model->id]])
			->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
			->all();		
       
		 if ((!empty($studentLessons)) && empty($model->vacationId)) {
            $this->addError($model,$attribute, 'Lesson time conflicts with student\'s another lesson');
		}
		if(!empty($model->vacationId) && !empty($studentLessons)) {
			foreach($studentLessons as $studentLesson) {
				if(new \DateTime($model->date) == new \DateTime($studentLesson->date) && (int) $studentLesson->status === Lesson::STATUS_SCHEDULED) {
					continue;
				}
				$conflictedLessonIds[] = $model->id; 
			}	
			if(!empty($conflictedLessonIds)) {
            	$this->addError($model,$attribute, 'Lesson time conflicts with student\'s another lesson');
			}
		}
    }
}
