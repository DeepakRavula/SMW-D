<?php

use common\models\UserProfile;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserProfile */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('backend', 'Edit profile')
?>

<div class="user-profile-form p-t-10">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row-fluid">
        <div class="col-md-2 flex">
        <?php echo $form->field($model, 'picture')->widget(\trntv\filekit\widget\Upload::classname(), [
            'url'=>['avatar-upload']
        ]) ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'firstname')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'middlename')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'lastname')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'gender')->dropDownlist([
            UserProfile::GENDER_FEMALE => Yii::t('backend', 'Female'),
            UserProfile::GENDER_MALE => Yii::t('backend', 'Male')
        ]) ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'locale')->dropDownlist(Yii::$app->params['availableLocales']) ?>
        </div>
        <div class="col-md-6">
            <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
