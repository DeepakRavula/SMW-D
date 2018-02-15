<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2Asset;
use yii\helpers\Url;

Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<div class="row user-create-form">
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['user/create', 'role_name' => $searchModel->role_name]),
        'id' => 'user-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'validationUrl' => Url::to(['user-contact/validate']),
    ]); ?>
    <div class="row">
        <?php echo $form->field($model, 'firstname') ?>
        <?php echo $form->field($model, 'lastname') ?>		
        <?= $form->field($emailModels, 'email')->textInput(['maxlength' => true])->label('Email (Work)') ?>
    </div>	
    <div class="row pull-right">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'pull-right btn btn-info', 'name' => 'signup-button']) ?>
        <?php echo Html::a('Cancel', '#', ['class' => 'pull-right m-r-10 btn user-add-cancel btn-default']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>
