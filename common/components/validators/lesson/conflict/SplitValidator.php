<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use common\models\Holiday;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeInclusive;

class SplitValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
       
        if (!empty($conflictedDatesResults)) {
            $this->addError($model,$attribute, 'Lesson date conflicts with holiday');
        }
    }
}