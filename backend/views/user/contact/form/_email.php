<?php

use yii\helpers\Html;
use common\models\Label;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\components\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<div class="row user-create-form">
<?php
  $url = Url::to(['user-contact/edit-email', 'id' => $model->id]);
    if ($model->isNewRecord) {
        $url = Url::to(['user-contact/create-email','id' => $userModel->id]);
    }
$form = ActiveForm::begin([
        'id' => 'email-form',
        'action' => $url,
                'enableAjaxValidation' => true,
        'enableClientValidation' => true,
                'validationUrl' => Url::to(['user-contact/validate', 'id' => $userModel->id]),
    ]);
?>
<div class="row">
		<?= $form->field($emailModel, "email")->textInput(['maxlength' => true]) ?>
		<?=
        $form->field($model, "labelId")->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Label::find()
                    ->user($userModel->id)
                    ->all(), 'id', 'name'),
            'options' => [
                'id' => 'email-label',
                'createNew' => true,
            ],
            'pluginOptions' => [
                'tags' => true,
                'placeholder' => 'select label'
            ],
        ])->label('Label');
        ?>
    
</div>
	<div class="row pull-right">
		<?php echo Html::a('Cancel', '#', ['class' => 'btn btn-default email-cancel-btn']); ?>        
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
	</div>
             <div class="pull-left">       
 <?php
                if (!$model->isNewRecord) {
                    echo Html::a('Delete', [
                '#', 'id' => $model->id
                ], [
                'id' => $model->id,
                'class' => 'user-contact-delete btn btn-danger',
            ]);
                }

        ?>
         </div>
<?php ActiveForm::end(); ?>
</div>
