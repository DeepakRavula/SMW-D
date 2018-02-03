<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;

class PastDateValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (new \DateTime($model->date) < new \DateTime() && !$model->isCanceled()) {
            $this->addError($model, $attribute, 'The lesson cannot be scheduled on past dates!');
        }
    }
}
