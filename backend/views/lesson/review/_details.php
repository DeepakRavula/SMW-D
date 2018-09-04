<?php

use common\models\Program;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\User;
use common\models\Enrolment;

?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Details',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<?php if ($courseModel) : ?>
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
		<?php $courseSchedules = $courseModel->courseSchedule; ?>
		<?php $courseSchedule = end($courseSchedules);?>
		<dd><?= (new \DateTime($courseSchedule->fromTime))->format('h:i A');?></dd>
	<?php elseif ($model->enrolmentIds) : ?>
	<?php $enrolment = Enrolment::findOne(end($model->enrolmentIds));
		$teacher = User::findOne($model->teacherId); 
	?>
		<dt>Program</dt>
		<dd><?= $enrolment->program->name; ?></dd>
		<dt>Teacher</dt>
		<dd><?= $teacher->publicIdentity; ?></dd>
	<?php endif; ?>
</dl>
<?php LteBox::end()?>