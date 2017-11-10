<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>

<?php
$boxTools = $this->render('_details-box-tools');
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => 'Details',
        'boxTools' => $boxTools,
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
	<dd><?= $model->programRate; ?></dd>
	<dt>Duration</dt>
	<dd><?= (new \DateTime($model->courseSchedule->duration))->format('H:i'); ?></dd>
</dl>
<?php LteBox::end()?>