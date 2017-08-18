<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil edit-lesson-detail"></i>',
	'title' => 'Details',
])
?>
<div class="col-xs-2 p-0"><strong>Program</strong></div>
<div class="col-xs-6">
	<?= $model->course->program->name; ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Classroom</strong></div>
<div class="col-xs-6">
<?= !empty($model->classroom->name) ? $model->classroom->name : 'None'; ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Status</strong></div>
<div class="col-xs-6">
	<?= $model->getStatus(); ?>
</div> 
<div class='clearfix'></div>
<?php
LteBox::end()?>