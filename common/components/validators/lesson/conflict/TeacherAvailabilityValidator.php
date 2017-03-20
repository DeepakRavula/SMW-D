<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use Yii;
use common\models\TeacherAvailability;
use common\models\Lesson;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeInclusive;

class TeacherAvailabilityValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
		$teacherLocationId   = $model->teacher->userLocation->id;
        $day                 = (new \DateTime($model->date))->format('N');
        $start               = new \DateTime($model->date);
        $duration            = $model->newDuration;
        $end                 = $start->add(new \DateInterval('PT' . $duration->format('H') . 'H' . $duration->format('i') . 'M'));
        $teacherAvailability = TeacherAvailability::find()
            ->andWhere(['day' => $day, 'teacher_location_id' => $teacherLocationId])
            ->andWhere(['AND',
                ['<=', 'from_time', $start->format('H:i:s')],
                ['>=', 'to_time', $end->format('H:i:s')]
            ])
            ->one();
		if(empty($teacherAvailability)) {
			$this->addError($model,$attribute, 'Teacher is not available on ' . $end->format('l') . ' at ' . $end->format('g:i A'));
		} else {
        	$locationId = Yii::$app->session->get('location_id');
			$teacherLessons = Lesson::find()
				->where([
					'lesson.status' => Lesson::STATUS_SCHEDULED,
					'lesson.teacherId' => $model->teacherId,
				])
				->andWhere(['NOT IN', 'courseId', $model->courseId])
				->location($locationId)
				->notDeleted()
				->all();
			$otherLessons = [];
			foreach ($teacherLessons as $teacherLesson) {
				$otherLessons[] = [
					'id' => $teacherLesson->id,
					'date' => $teacherLesson->date,
					'duration' => $teacherLesson->course->duration,
				];
			}
			$intervals = [];
			foreach ($otherLessons as $otherLesson) {
				$intervals[] = new DateRangeInclusive(new \DateTime($otherLesson['date']), new \DateTime($otherLesson['date']), new \DateInterval('PT'.$duration->format('H').'H'.$duration->format('i').'M'), $otherLesson['id']);
			}
			$tree = new IntervalTree($intervals);
			$conflictedLessonsResults = $tree->search(new \DateTime($model->date));

        if ((!empty($conflictedLessonsResults))) {
            $this->addError($model,$attribute, 'Teacher occupied with another lesson');
        }
		} 
    }
}