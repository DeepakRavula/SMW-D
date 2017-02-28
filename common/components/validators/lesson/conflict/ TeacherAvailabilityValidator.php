<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use Yii;
use common\models\TeacherAvailability;

class TeacherAvailabilityValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
		$locationId = $model->teacher->userLocation->location_id; 
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
		} 
    }
}