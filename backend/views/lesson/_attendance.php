<?php
\insolita\wgadminlte\LteBox::begin([
	'type' => \insolita\wgadminlte\LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil"></i>',
	'title' => 'Attendance',
])
?>
<strong>Present</strong>
<?= $model->getPresent(); ?>	
<?php \insolita\wgadminlte\LteBox::end() ?>
					