<?php 
use common\models\Program;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php if (!empty($vacationId)) : ?>
	<?php $url = Url::to(['confirm',
		'courseId' => $courseId, 
		'Vacation[id]' => $vacationId,
		'Vacation[type]' => $vacationType
	]);?>
<?php elseif (!empty($rescheduleBeginDate) && !empty($rescheduleEndDate)): ?>
	<?php $url = Url::to(['confirm', 
		'courseId' => $courseId, 
		'Course[startDate]' => $rescheduleBeginDate,
		'Course[endDate]' => $rescheduleEndDate
	]);?>
<?php elseif (!empty($enrolmentType)): ?>
	<?php $url = Url::to(['confirm', 
		'courseId' => $courseId, 
		'Enrolment[type]' => $enrolmentType
	]);?>
<?php else: ?>
	<?php $url = Url::to(['confirm', 'courseId' => $courseId]);?>
<?php endif; ?>
<div class="form-group">
	<div class="p-10 text-center">
		<?=
		Html::a('Confirm', $url, [
			'class' => 'btn btn-danger',
			'id' => 'confirm-button',
			'disabled' => $hasConflict,
			'data' => [
				'method' => 'post',
			],
		])
		?>
		<?php if ((int) $courseModel->program->isPrivate() && empty($enrolmentType)) : ?>
			<?= Html::a('Cancel', ['student/view', 'id' => $courseModel->enrolment->studentId], ['class' => 'btn']);
			?>
		<?php elseif(!empty($enrolmentType)) : ?>
			<?= Html::a('Cancel', ['enrolment/index'], ['class' => 'btn']);
			?>
		<?php else :?>
			<?= Html::a('Cancel', ['course/index'], ['class' => 'btn']);
			?>
		<?php endif; ?>
	</div>
</div>