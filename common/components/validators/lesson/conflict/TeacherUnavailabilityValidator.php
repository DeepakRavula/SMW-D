<?php
namespace common\components\validators\lesson\conflict;

use common\models\Lesson;
use common\models\TeacherUnavailability;
use common\models\Location;
use yii\validators\Validator;
use Carbon\Carbon;

class TeacherUnavailabilityValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {    
            $fromTime = (new \DateTime($model->fromDateTime));
            $fromTime->modify('+1 second');
            $start = $fromTime->format('Y-m-d H:i:s');
            $toTime = (new \DateTime($model->toDateTime));
            $toTime ->modify('-1 second');
            $end = $toTime->format('Y-m-d H:i:s');
            $teacherId = [$model->teacherId];
            $unavailabilities = TeacherUnavailability::find()
            ->andWhere(['teacherId' => $teacherId])
            ->overlapwithtime($start, $end)
            ->all();
            if(!empty($unavailabilities)){
               return $this->addError($model, $attribute, 'Teacher unavailability is overlapped');
            }
        
    }
}