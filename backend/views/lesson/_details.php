<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
?>
<?php Pjax::begin([
	'id' => 'lesson-detail'
]);?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil edit-lesson-detail"></i>',
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
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>