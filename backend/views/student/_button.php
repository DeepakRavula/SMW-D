<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
<div id="show-all" class="checkbox-btn">
<?= $form->field($searchModel, 'showAllStudents')->checkbox(['data-pjax' => true]); ?>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>