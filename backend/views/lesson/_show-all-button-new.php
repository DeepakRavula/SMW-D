<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<?php
$form = ActiveForm::begin([
        'action' => ['new-index'],
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
    <div class="show-all-top m-r-20">
    <div class="checkbox">
    <div id="show-all-new" class="checkbox-btn">
        <?= $form->field($searchModel, 'showAll')->checkbox(['data-pjax' => true]); ?>
    </div>
    </div>
    </div>
<li>
</ul>
</div>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>