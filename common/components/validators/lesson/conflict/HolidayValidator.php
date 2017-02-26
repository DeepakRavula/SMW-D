<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use common\models\Holiday;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeInclusive;

class HolidayValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
       $holidays = Holiday::find()
            ->all();

        $intervals = [];
        foreach ($holidays as $holiday) {
            $intervals[] = new DateRangeInclusive(new \DateTime($holiday->date), new \DateTime($holiday->date));
        }	
		$tree = new IntervalTree($intervals);
        $conflictedDatesResults = $tree->search(new \DateTime($model->$attribute));
        
        if (!empty($conflictedDatesResults)) {
            $this->addError($model,$attribute, 'Lesson date conflicts with holiday');
        }
    }
}