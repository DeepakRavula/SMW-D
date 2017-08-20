<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => 'Details',
	'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Program</dt>
	<dd>
	<?= $model->course->program->name; ?>
	</dd>
	<dt>Teacher</dt>
	<dd><?= $model->course->teacher->publicIdentity; ?></dd>
	<dt>Rate</dt>
	<dd><?= $model->course->program->rate; ?></dd>
	<dt>Duration</dt>
	<dd><?= (new \DateTime($model->courseSchedule->duration))->format('H:i'); ?></dd>
</dl>
<?php LteBox::end()?>