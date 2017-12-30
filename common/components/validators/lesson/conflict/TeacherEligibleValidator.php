<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use common\models\User;
use yii\helpers\ArrayHelper;

class TeacherEligibleValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        if (!in_array($model->teacherId, ArrayHelper::getColumn(User::find()
            ->teachers($model->course->programId, $locationId)->notDeleted()->all(), 'id'))) {
            $this->addError($model, $attribute, 'Please choose an eligible
                teacher who is qualified to teach ' . $model->course->program->name .'!');
        }
    }
}
