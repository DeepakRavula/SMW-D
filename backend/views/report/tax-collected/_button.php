<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<?php
$form = ActiveForm::begin([
        'action' => ['tax-collected'],
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
    <?= $form->field($model, 'summarizeResults')->checkbox(['data-pjax' => true]); ?>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>