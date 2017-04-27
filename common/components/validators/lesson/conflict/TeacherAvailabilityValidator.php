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
        $locationId = Yii::$app->session->get('location_id');
        $day                 = (new \DateTime($model->date))->format('N');
        $start               = new \DateTime($model->date);
		$lessonDate = (new \DateTime($model->date))->format('Y-m-d');
		$lessonStartTime = $start->format('H:i:s');
		$duration = $model->newDuration->format('H:i:s'); 
		$lessonDuration = explode(':', $duration);
		$start->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));	
		$start->modify('-1 second');
		$lessonEndTime = $start->format('H:i:s');
        $teacherAvailability = TeacherAvailability::find()
            ->andWhere(['day' => $day, 'teacher_location_id' => $teacherLocationId])
            ->andWhere(['AND',
                ['<=', 'from_time', $lessonStartTime],
                ['>=', 'to_time', $lessonEndTime]
            ])
            ->one();
		
		if(empty($teacherAvailability)) {
			$this->addError($model,$attribute, 'Teacher is not available');
		} else {
			$teacherLessons = Lesson::find()
				->teacherLessons($locationId, $model->teacherId)
				->andWhere(['NOT', ['lesson.id' => $model->id]])
				->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
				->all();
			if ((!empty($teacherLessons))) {
				$this->addError($model,$attribute, 'Teacher occupied with another lesson');
			}
		} 
    }
}