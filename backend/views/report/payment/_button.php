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
<?= $form->field($model, 'groupByMethod')->checkbox(['data-pjax' => true, 'id' => 'group-by-method']); ?>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>