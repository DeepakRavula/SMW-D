<?php

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
<div id="show-all" class="m-r-10 mail-flag">
<?=
    $form->field($model, 'isSent')->widget(
        SwitchInput::classname(),
        [
        'name' => 'isSent',
        'pluginOptions' => [
            'handleWidth' => 50,
            'onText' => 'Sent',
            'offText' => 'Not Sent',
			'size' => 'mini'
        ],
    ]
    )->label(false);?>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>