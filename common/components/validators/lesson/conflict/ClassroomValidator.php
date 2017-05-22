<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use Yii;
use common\models\ClassroomUnavailability;
use common\models\Lesson;

class ClassroomValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $lessonDate = (new \DateTime($model->date))->format('Y-m-d');

		$classroomUnavailabilities = ClassroomUnavailability::find()
			->andWhere(['classroomId' => $model->classroomId])
			->andWhere(['AND',
				['<=', 'DATE(fromDate)', $lessonDate],
				['>=', 'DATE(toDate)', $lessonDate]
			])
			->all();
		if(!empty($classroomUnavailabilities)) {
            $this->addError($model, $attribute, 'Classroom is unavailable');
		}
    }
}
