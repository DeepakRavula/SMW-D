<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeInclusive;
use common\models\Lesson;

class StudentValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
       	$locationId = Yii::$app->session->get('location_id');
        $otherLessons = [];
        $intervals = [];

		$studentLessons = Lesson::find()
			->studentLessons($locationId, $model->course->enrolment->student->id)
			->all();

		foreach ($studentLessons as $studentLesson) {
			$otherLessons[] = [
				'id' => $studentLesson->id,
				'date' => $studentLesson->date,
				'duration' => $studentLesson->course->duration,
			];
		}
		foreach ($otherLessons as $otherLesson) {
            $timebits = explode(':', $otherLesson['duration']);
            $intervals[] = new DateRangeInclusive(new \DateTime($otherLesson['date']), new \DateTime($otherLesson['date']), new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'), $otherLesson['id']);
        }
		$tree = new IntervalTree($intervals);
        $conflictedLessonsResults = $tree->search(new \DateTime($model->$attribute));
		
        if ((!empty($conflictedLessonsResults))) {
            $this->addError($model,$attribute, 'Lesson time conflicts with student\'s another lesson');
        }
    }
}