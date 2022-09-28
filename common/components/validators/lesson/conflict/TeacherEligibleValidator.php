<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\Location;

class TeacherEligibleValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $hasCookie = Yii::$app->getRequest()->getCookies()->has('locationId');
        if($hasCookie){
           $locationId = Yii::$app->getRequest()->getCookies()->getValue('locationId');
        }
        if (!in_array($model->teacherId, ArrayHelper::getColumn(User::find()
            ->teachers($model->course->programId, $locationId)->notDeleted()->all(), 'id'))) {
            $this->addError($model, $attribute, 'Please choose an eligible
                teacher who is qualified to teach ' . $model->course->program->name .'!');
        }
    }
}
