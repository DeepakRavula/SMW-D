<?php

use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="row user-create-form">
<?php $form = ActiveForm::begin(['id' => 'attendance-form',
	'action' => Url::to(['private-lesson/update-attendance', 'id' => $model->id])]); ?>
<div class="row">
	<?= 
	$form->field($model, 'isPresent')->widget(SwitchInput::classname(), [
		'name' => 'present',
		'pluginOptions' => [
			'handleWidth' => 61,
			'onText' => 'Present',
			'offText' => 'Absent',
		],
	])->label(false);
	?>
</div>
<div class="row pull-right">
  <?= Html::a('Cancel', '#', ['class' => 'btn btn-default attendance-cancel']);?> 
  <?= Html::submitButton(Yii::t('backend', 'Save'), ['id' => 'lesson-edit-save', 'class' => 'btn btn-info', 'name' => 'button']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>