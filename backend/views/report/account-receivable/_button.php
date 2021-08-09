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
<div id="bulk-action-menu" class="m-b-10 pull-right">
    <div class="btn-group">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-filter fa-1x"></i>&nbsp;&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li>
         
            <?= $form->field($searchModel, 'showAllActive')->label(false)->checkbox(['label' => 'Active Customers','data-pjax' => true]); ?>
            <?= $form->field($searchModel, 'showAllInActive')->label(false)->checkbox(['label' => 'Inactive Customers','data-pjax' => true  ]); ?>
           
</li>
        </ul>
    </div>
</div>
<?php Pjax::end(); ?>
<?php ActiveForm::end(); ?>