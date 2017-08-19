<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => 'Details',
])
?>
<div class="col-xs-2 p-0"><strong>Program</strong></div>
<div class="col-xs-6">
	<?= $model->course->program->name; ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Teacher</strong></div>
<div class="col-xs-6">
<?= $model->course->teacher->publicIdentity; ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Rate</strong></div>
<div class="col-xs-6">
	<?= $model->course->program->rate; ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Duration</strong></div>
<div class="col-xs-6">
	<?= (new \DateTime($model->courseSchedule->duration))->format('H:i'); ?>
</div> 
<?php LteBox::end()?>