<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeExclusive;
use common\models\Lesson;

class StudentValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
		if($model->course->program->isPrivate()) {
			$studentId = $model->course->enrolment->student->id; 
		}
		$studentId = !empty($model->studentId) ? $model->studentId : null;
       	$locationId = Yii::$app->session->get('location_id');
        $otherLessons = [];
        $intervals = [];

		$studentLessons = Lesson::find()
			->studentLessons($locationId, $studentId)
			->all();

		foreach ($studentLessons as $studentLesson) {
			if(!empty($model->vacationId) && new \DateTime($studentLesson->date) == new \DateTime($model->date) && $studentLesson->isScheduled()) {
				continue;
			}
			$otherLessons[] = [
				'id' => $studentLesson->id,
				'date' => $studentLesson->date,
				'duration' => $studentLesson->course->duration,
			];
		}
		foreach ($otherLessons as $otherLesson) {
            $timebits = explode(':', $otherLesson['duration']);
			$endDate = new \DateTime($otherLesson['date']);
			$endDate->add(new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'));
            $intervals[] = new DateRangeExclusive(new \DateTime($otherLesson['date']),$endDate,null, $otherLesson['id']);
        }
		$tree = new IntervalTree($intervals);
		$duration = explode(':', $model->duration);
		$toDate = new \DateTime($model->date);
		$toDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
		$searchRange = new DateRangeExclusive(new \DateTime($model->date), $toDate);
        $conflictedLessonsResults = $tree->search($searchRange);
		
        if ((!empty($conflictedLessonsResults))) {
            $this->addError($model,$attribute, 'Lesson time conflicts with student\'s another lesson');
        }
    }
}