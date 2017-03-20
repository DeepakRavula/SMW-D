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
		$studentLessons = Lesson::find()
		->studentEnrolment($locationId, $studentId)
		->where(['lesson.status' => Lesson::STATUS_SCHEDULED])
		->andWhere(['NOT IN', 'lesson.courseId', $model->courseId])
		->notDeleted()
		->all();
		$otherLessons = [];
		$duration            = $model->newDuration;
		foreach ($studentLessons as $studentLesson) {
			$otherLessons[] = [
				'id' => $studentLesson->id,
				'date' => $studentLesson->date,
				'duration' => $studentLesson->course->duration,
			];
		}
		foreach ($otherLessons as $otherLesson) {
			$intervals[] = new DateRangeInclusive(new \DateTime($otherLesson['date']), new \DateTime($otherLesson['date']), new \DateInterval('PT'.$duration->format('H').'H'.$duration->format('i').'M'), $otherLesson['id']);
		}
		print_r($intervals);die;
		$tree = new IntervalTree($intervals);
		$conflictedLessonsResults = $tree->search(new \DateTime($model->date));
		print_r($conflictedLessonsResults);die;

        if ((!empty($conflictedLessonsResults))) {
            $this->addError($model,$attribute, 'Student occupied with another lesson');
        }
	} 
}