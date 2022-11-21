<?php

namespace common\components\validators\classroom;

use yii\validators\Validator;
use Yii;
use common\models\ClassroomUnavailability;
use common\models\Location;

class ClassroomUnavailabilityValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $locationId        = Location::findOne(['slug' => \Yii::$app->location])->id;
        $dateRange         = $model->dateRange;
        list($fromDate, $toDate) = explode(' - ', $dateRange);

        $fromDate          = \DateTime::createFromFormat('M d,Y', $fromDate)->format('Y-m-d');
        $toDate            = \DateTime::createFromFormat('M d,Y', $toDate)->format('Y-m-d');
        $currentDate       = (new \DateTime())->format('Y-m-d');
        if (($fromDate < $currentDate || $toDate < $currentDate)) {
            return $this->addError(
                $model,
                $attribute,
                    'Unavailability cannot be set for past dates'
            );
        }
    }
}
