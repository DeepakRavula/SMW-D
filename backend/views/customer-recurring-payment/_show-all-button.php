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
<div id="bulk-action-menu" class="m-b-10 pull-right">
    <div class="btn-group">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-filter fa-1x"></i>&nbsp;&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li>
            <?= $form->field($searchModel, 'showAll')->label(false)->checkbox(['label' => 'Show Inactive Items','data-pjax' => true]); ?>
            </li>
        </ul>
    </div>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>