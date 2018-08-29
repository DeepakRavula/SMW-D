<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'fieldConfig' => [
        'options' => [
            'tag' => false
        ]
    ],
]); ?>

<?php Pjax::begin() ?>

<div class="show-all-top">
    <div class="checkbox">
        <div id="show-all">
            <?= $form->field($searchModel, 'showAllEnrolments')->checkbox(['data-pjax' => true]); ?>
        </div>
    </div>
</div>

<?php Pjax::end(); ?>

<?php ActiveForm::end(); ?>