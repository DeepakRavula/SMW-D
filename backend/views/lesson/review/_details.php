<?php

use common\models\Program;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Details',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
    <?php if ((int) $courseModel->program->type === Program::TYPE_PRIVATE_PROGRAM) :?>
		<dt>Student</dt>
		<dd><?= $courseModel->enrolment->student->fullName; ?></dd>
    <?php endif; ?>
	<dt>Program</dt>
	<dd><?= $courseModel->program->name; ?></dd>
	<dt>Teacher</dt>
	<dd><?= $courseModel->teacher->publicIdentity; ?></dd>
	<dt>Period</dt>
	<dd><?= Yii::$app->formatter->asDate($courseModel->startDate) . ' to ' . Yii::$app->formatter->asDate($courseModel->endDate)?></dd>
	<dt>Time</dt>
	<dd><?= (new \DateTime($courseModel->courseSchedule->fromTime))->format('h:i A');?></dd>
</dl>
<?php LteBox::end()?>