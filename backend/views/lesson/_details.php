<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use yii\helpers\Html;
use kartik\color\ColorInput;
?>
<?php Pjax::begin([
    'id' => 'lesson-detail',
    'timeout' => 6000,
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
         <dt> Color Code</dt>
         <dd>  <?=  Html::input('text', 'colorcode', '', ['class' => $model->getClass().' lesson-colorcode','style'=>'background:'.$model->getColorCode().';']); ?></dd>
    <dt>Online</dt>
    <dd>
     <?= $model->privateLesson->is_online ? "Yes" : 'No' ; ?>
    </dd>
   
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>