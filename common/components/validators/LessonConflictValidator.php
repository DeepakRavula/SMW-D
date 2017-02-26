<?php
namespace common\components\validators;

use yii\validators\Validator;
use common\models\User;

class LessonConflictValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
       	$value = 10;
        if (!User::find()->where(['id' => $value])->exists()) {
            $model->addError($attribute, 'Invalid record');
        } 
    }
}