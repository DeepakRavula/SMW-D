<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;

class PastDateValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {   
	if(new \DateTime($model->date) < new \DateTime()) {
            $this->addError($model,$attribute, 'Lesson cannot be schedule on past dates!');
        }
    }
}