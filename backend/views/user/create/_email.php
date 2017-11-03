<?php

use yii\helpers\Html;
use common\models\Label;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
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
	]);
?>
<div class="row">
		<?= $form->field($emailModel, "email")->textInput(['maxlength' => true]) ?>
		<?=
		$form->field($model, "labelId")->widget(Select2::classname(), [
			'data' => ArrayHelper::map(Label::find()
					->user($userModel->id)
					->all(), 'id', 'name'),
			'options' => ['placeholder' => 'Select Label'],
			'pluginOptions' => [
				'tags' => true,
				'allowClear' => true,
			],
		])->label('Label');
		?>
</div>
	<div class="row pull-right">
		<?php echo Html::a('Cancel', '#', ['class' => 'btn btn-default email-cancel-btn']); ?>        
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
	</div>
<?php ActiveForm::end(); ?>
</div>
