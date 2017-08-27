<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;
?>
<?php
$form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	'fieldConfig' => [
        'options' => [
            'tag' => false,
        ],
    ],
	]);
?>
<?php yii\widgets\Pjax::begin() ?>
<div id="show-all" class="m-r-10">
<?=
	$form->field($model, 'isSent')->widget(SwitchInput::classname(),
		[
		'name' => 'isSent',
		'pluginOptions' => [
			'handleWidth' => 60, 
			'onText' => 'Sent',
			'offText' => 'Not Sent',
		],
	])->label(false);?>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>