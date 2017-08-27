<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;

?>
<?php $boxTools = null;?>
<?php if (!$model->isGroup()) : ?>
	<?php $form = ActiveForm::begin(['id' => 'lesson-present-form']); ?>
	<?php $model->present = $model->isMissed() ? false : true; 
	$disabled = $model->isMissed() ? true : false;
	?> 
	<?php 
	$boxTools = 
	$form->field($model, 'present')->widget(SwitchInput::classname(), [
		'name' => 'present',
		'disabled' => $disabled,
		'pluginOptions' => [
			'handleWidth' => 61,
			'onText' => 'Present',
			'offText' => 'Absent',
		],
	])->label(false);
	?>
<?php ActiveForm::end(); ?>
	<?php endif; ?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => 'Attendance',
	'boxTools' => $boxTools,
	'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Present</dt>
	<dd><?= $model->getPresent(); ?></dd>
</dl>
<?php LteBox::end() ?>
					