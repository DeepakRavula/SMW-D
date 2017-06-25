<?php 
use common\models\Program;
use yii\helpers\Html;

?>
<div class="form-group">
	<div class="p-10 text-center">
		<?php if (!empty($vacationId)) : ?>
			<?=
			Html::a('Confirm', ['confirm', 'courseId' => $courseId, 'Vacation[id]' => $vacationId, 'Vacation[type]' => $vacationType], [
				'class' => 'btn btn-danger',
				'id' => 'confirm-button',
				'disabled' => $hasConflict,
				'data' => [
					'method' => 'post',
				],
			])
			?>
		<?php elseif (!empty($rescheduleBeginDate)): ?>
			<?=
			Html::a('Confirm', ['confirm', 'courseId' => $courseId, 'Course[rescheduleBeginDate]' => $rescheduleBeginDate], [
				'class' => 'btn btn-danger',
				'id' => 'confirm-button',
				'disabled' => $hasConflict,
				'data' => [
					'method' => 'post',
				],
			])
			?>
		<?php else : ?>
			<?=
			Html::a('Confirm', ['confirm', 'courseId' => $courseId], [
				'class' => 'btn btn-danger',
				'id' => 'confirm-button',
				'disabled' => $hasConflict,
				'data' => [
					'method' => 'post',
				],
			])
			?>
		<?php endif; ?>
		<?php if ((int) $courseModel->program->type === Program::TYPE_PRIVATE_PROGRAM) : ?>
			<?= Html::a('Cancel', ['student/view', 'id' => $courseModel->enrolment->studentId], ['class' => 'btn']);
			?>
		<?php else : ?>
			<?= Html::a('Cancel', ['course/index'], ['class' => 'btn']);
			?>
		<?php endif; ?>
	</div>
</div>