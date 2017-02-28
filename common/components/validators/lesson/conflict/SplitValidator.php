<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use Yii;
use common\models\TeacherAvailability;

class SplitValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
		$duration = $model->newDuration->format('H:i:s');
		$timebits = explode(':', $duration);
		$lessonDate = new \DateTime($model->date);
		$lessonDate->add(new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'));
		$time = $lessonDate->format('H:i:s');
		$locationId = Yii::$app->session->get('location_id');
		$day = (new \DateTime($model->date))->format('N');
      	$teacherAvailabilities = TeacherAvailability::find()
			->joinWith(['userLocation' => function($query) use($model, $locationId){
				$query->andWhere(['user_id' => $model->teacherId, 'location_id' => $locationId]);
			}])
			->andWhere(['day' => $day])
			->all();
			foreach($teacherAvailabilities as $teacherAvailability) {
				if($time >= $teacherAvailability->from_time && $time <= $teacherAvailability->to_time) {
					continue;
				} else {
					$this->addError($model,$attribute, 'Teacher is not available at ' . $model->newDuration->format('g:i A'));
				}
			} 
    }
}