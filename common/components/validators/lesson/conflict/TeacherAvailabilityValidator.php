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
		$lessonDuration = explode(':', $model->duration);
		$lessonStart = (new \DateTime($model->date));
		$lessonStart->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));	
		$intervals = [];
		$oldDuration = new \DateTime($model->duration);
		$durationDifference = $duration->diff($oldDuration);	
		
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
				->teacherLessons($locationId, $model->teacherId)
				->andWhere(['NOT IN', 'lesson.id', $model->id])
				->all();
			
			$otherLessons = [];
			foreach ($teacherLessons as $teacherLesson) {
				$otherLessons[] = [
					'id' => $teacherLesson->id,
					'date' => $teacherLesson->date,
					'duration' => $teacherLesson->course->duration,
				];
			}
			foreach ($otherLessons as $otherLesson) {
				$intervals[] = new DateRangeInclusive(new \DateTime($otherLesson['date']), new \DateTime($otherLesson['date']), new \DateInterval('PT'.$durationDifference->h.'H'.$durationDifference->i.'M'), $otherLesson['id']);
			}
			$tree = new IntervalTree($intervals);
			$conflictedLessonsResults = $tree->search($lessonStart);

        if ((!empty($conflictedLessonsResults))) {
            $this->addError($model,$attribute, 'Teacher occupied with another lesson');
        }
		} 
    }
}