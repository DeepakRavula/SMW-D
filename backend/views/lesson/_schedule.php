<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil"></i>',
	'title' => 'Schedule',
])
?>
<div class="col-xs-2 p-0"><strong>Date</strong></div>
<div class="col-xs-6">
<?= (new \DateTime($model->date))->format('l, F jS, Y'); ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Time</strong></div>
<div class="col-xs-6">
<?= Yii::$app->formatter->asTime($model->date); ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Duration</strong></div>
<div class="col-xs-6">
<?= (new \DateTime($model->duration))->format('H:i'); ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Classroom</strong></div>
<div class="col-xs-6">
<?= !empty($model->classroom->name) ? $model->classroom->name : 'None'; ?>
</div> 
<div class='clearfix'></div>
<?php LteBox::end() ?>
