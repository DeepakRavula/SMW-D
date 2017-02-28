<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use common\models\Holiday;
use common\models\Program;
use common\models\Lesson;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeInclusive;

class ReviewValidator extends Validator
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
            $intervals[] = new DateRangeInclusive(new \DateTime($otherLesson['date']), new \DateTime($otherLesson['date']), new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'), $otherLesson['id']);
        }
		
        $tree = new IntervalTree($intervals);
        $conflictedLessonsResults = $tree->search(new \DateTime($model->date));
        
        if (!empty($conflictedLessonsResults)) {
            $this->addError($model, $attribute, 'Lesson time conflicts with same enrolment lesson');
        }
    }
}