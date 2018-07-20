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
<div class="show-all-top m-r-20">
<div class="checkbox">
<div id="show-all" class="checkbox-btn">
<?= $form->field($searchModel, 'showAll')->checkbox(['data-pjax' => true]); ?>
</div>
</div>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>