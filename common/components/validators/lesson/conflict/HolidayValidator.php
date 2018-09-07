<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use common\models\Holiday;
use Yii;
use yii\helpers\ArrayHelper;

class HolidayValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $currentDate = new \DateTime();
        $currentDate->setDate($currentDate->format('Y'), 01, 01);
        $firstDayOfCurrentYear = $currentDate->format('Y-m-d');
        $holidays = Holiday::find()
            ->notDeleted()
            ->andWhere(['>=', 'DATE(date)', $firstDayOfCurrentYear])
            ->all();
        $holidayDates = ArrayHelper::getColumn($holidays, function ($element) {
            return (new \DateTime($element->date))->format('Y-m-d');
        });
        $lessonDate = (new \DateTime($model->date))->format('Y-m-d');
        if (in_array($lessonDate, $holidayDates)) {
            $this->addError($model, $attribute, 'Lesson date conflicts with holiday');
        }
    }
}
