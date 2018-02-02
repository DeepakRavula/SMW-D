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
<?= $form->field($searchModel, 'showAllCourses')->checkbox(['data-pjax' => true]); ?>
</div>
<div class="m-b-10">
    <div class="btn-group">
        <button class="btn dropdown-toggle" data-toggle="dropdown">Bulk Action&nbsp;&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="substitute-teacher" href="#">Substitute Teacher</a></li>
        </ul>
    </div>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>