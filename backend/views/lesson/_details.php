<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use yii\helpers\Html;
use kartik\color\ColorInput;
?>
<?php Pjax::begin([
	'id' => 'lesson-detail'
]);?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => '<i title="Edit" class="fa fa-pencil edit-lesson-detail"></i>',
	'title' => 'Details',
	'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Program</dt>
	<dd><?= $model->course->program->name; ?></dd>
	<dt>Classroom</dt>
	<dd><?= !empty($model->classroom->name) ? $model->classroom->name : 'None'; ?></dd>
	<dt>Status</dt>
	<dd><?= $model->getStatus(); ?></dd>
    <div class="row">
     <div class="col-md-4">
         <dt> Color Code</dt>
     </div>
     <div class="col-md-4">  
    <?php echo ColorInput::widget([
    'model' => $model, 
    'name'=>'colorcode',
    'value' => $model->getColorCode(),   
    'disabled'=>true,  
        ]);?>
    </div>    
    </div>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>