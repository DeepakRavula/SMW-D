<?php

use wbraganca\dynamicform\DynamicFormWidget;
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
$form = ActiveForm::begin([
		'id' => 'phone-form',
		'action' => Url::to(['user-contact/create-phone', 'id' => $model->id])
	]);
?>
<div class="row">
	<?= $form->field($phoneModel, "number")->textInput(['maxlength' => true]) ?>
	<?=
	$form->field($userContact, "labelId")->widget(Select2::classname(), [
		'data' => ArrayHelper::map(Label::find()
				->user($model->id)
				->all(), 'id', 'name'),
		'options' => [
			'id' => 'phone-label',
			'placeholder' => 'Select Label'],
		'pluginOptions' => [
			'tags' => true,
			'allowClear' => true,
		],
	])->label('Label');
	?>
	<?= $form->field($phoneModel, "extension")->textInput(['maxlength' => true]) ?>
</div>
<div class="row pull-right">
	<?php echo Html::a('Cancel', '#', ['class' => 'm-r-10 btn btn-default phone-cancel-btn']); ?>
<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
