<?php

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
        'action' => Url::to(['user/create', 'role_name' => $model->roles]),
        'id' => 'modal-form',
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['user-contact/validate']),
    ]); ?>
    <div class="row">
        <?php echo $form->field($model, 'firstname') ?>
        <?php echo $form->field($model, 'lastname') ?>		
        <?= $form->field($emailModel, 'email')->textInput(['maxlength' => true])->label('Email (Work)') ?>
    </div>
<?php ActiveForm::end(); ?>
</div>
