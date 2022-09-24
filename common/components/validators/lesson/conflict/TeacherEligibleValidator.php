<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\Location;
use common\helpers\CacheHelper;

class TeacherEligibleValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $query = Location::find()->andWhere(['slug' => \Yii::$app->location]);
        $locationId = CacheHelper::CacheOne($query)->id;
        if (!in_array($model->teacherId, ArrayHelper::getColumn(User::find()
            ->teachers($model->course->programId, $locationId)->notDeleted()->all(), 'id'))) {
            $this->addError($model, $attribute, 'Please choose an eligible
                teacher who is qualified to teach ' . $model->course->program->name .'!');
        }
    }
}
