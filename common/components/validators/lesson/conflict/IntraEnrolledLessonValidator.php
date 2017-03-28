<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use common\models\Lesson;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeExclusive;

class IntraEnrolledLessonValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
		$intervals = [];
		$otherLessons = [];
      	$draftLessons = Lesson::find()
            ->where(['courseId' => $model->courseId, 'status' => Lesson::STATUS_DRAFTED])
            ->andWhere(['NOT', ['id' => $model->id]])
            ->all();
        foreach ($draftLessons as $draftLesson) {
            $otherLessons[] = [
                'id' => $draftLesson->id,
                'date' => $draftLesson->date,
                'duration' => $draftLesson->course->duration,
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
        
        if (!empty($conflictedLessonsResults)) {
            $this->addError($model, $attribute, 'Lesson time conflicts with same enrolment lesson');
        }
    }
}