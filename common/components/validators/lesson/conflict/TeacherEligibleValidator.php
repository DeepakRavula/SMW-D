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
        $locationId = Yii::$app->filecache->get('locationId' . $model->courseId);
            if($locationId == false)
            {
                $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
                Yii::$app->filecache->set('locationId' . $model->courseId,$locationId, 60);
            }
        if (!in_array($model->teacherId, ArrayHelper::getColumn(User::find()
            ->teachers($model->course->programId, $locationId)->notDeleted()->all(), 'id'))) {
            $this->addError($model, $attribute, 'Please choose an eligible
                teacher who is qualified to teach ' . $model->course->program->name .'!');
        }
    }
}
