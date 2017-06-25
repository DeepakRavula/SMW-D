<?php ?>
<?php
\insolita\wgadminlte\LteBox::begin([
	'type' => \insolita\wgadminlte\LteConst::TYPE_DEFAULT,
	'title' => 'Schedule',
])
?>
<strong>Date</strong>
<?= (new \DateTime($model->date))->format('l, F jS, Y'); ?>
<div class="clearfix"></div>
<strong>Time</strong>
<?= Yii::$app->formatter->asTime($model->date); ?>
<div class="clearfix"></div>
<strong>Duration</strong>
<?= (new \DateTime($model->duration))->format('H:i'); ?>
<div class="clearfix"></div>
<strong>Classroom</strong>
<?= !empty($model->classroom->name) ? $model->classroom->name : 'None'; ?>
<?php \insolita\wgadminlte\LteBox::end() ?>
