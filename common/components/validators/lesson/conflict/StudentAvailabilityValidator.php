<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use Yii;
use common\models\TeacherAvailability;
use common\models\Lesson;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeInclusive;

class StudentAvailabilityValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
		$locationId = Yii::$app->session->get('location_id');
		$studentId = $model->course->enrolment->student->id; 
		$duration            = $model->newDuration;
		$lessonDuration = explode(':', $model->duration);
		$lessonStart = (new \DateTime($model->date));
		$lessonStart->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));	
		$intervals = [];
		$oldDuration = new \DateTime($model->duration);
		$durationDifference = $duration->diff($oldDuration);	
		
		$studentLessons = Lesson::find()
			->studentEnrolment($locationId, $studentId)
			->where(['lesson.status' => Lesson::STATUS_SCHEDULED])
			->andWhere(['NOT IN', 'lesson.courseId', $model->courseId])
			->andWhere(['NOT IN', 'lesson.id', $model->id])
			->notDeleted()
			->all();
		
		$otherLessons = [];
		foreach ($studentLessons as $studentLesson) {
			$otherLessons[] = [
				'id' => $studentLesson->id,
				'date' => $studentLesson->date,
				'duration' => $studentLesson->course->duration,
			];
		}
		foreach ($otherLessons as $otherLesson) {
			$intervals[] = new DateRangeInclusive(new \DateTime($otherLesson['date']), new \DateTime($otherLesson['date']), new \DateInterval('PT'.$durationDifference->h.'H'.$durationDifference->i.'M'), $otherLesson['id']);
		}
		$tree = new IntervalTree($intervals);
		$conflictedLessonsResults = $tree->search($lessonStart);

        if ((!empty($conflictedLessonsResults))) {
            $this->addError($model,$attribute, 'Student occupied with another lesson');
        }
	} 
}