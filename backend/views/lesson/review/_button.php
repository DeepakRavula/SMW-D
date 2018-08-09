<?php 
use common\models\Program;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php if (!empty($rescheduleBeginDate) && !empty($rescheduleEndDate)): ?>
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
            'class' => 'btn btn-info',
            'id' => 'confirm-button',
            'disabled' => $hasConflict,
            'data' => [
                'method' => 'post',
            ],
        ])
        ?>
		<?php if ((int) $courseModel->program->isPrivate() && empty($enrolmentType)) : ?>
			<?= Html::a('Cancel', ['student/view', 'id' => $courseModel->enrolment->studentId], ['class' => 'btn btn-default review-cancel']);
            ?>
		<?php elseif (!empty($enrolmentType)) : ?>
			<?= Html::a('Cancel', ['enrolment/cancel', 'id' => $courseModel->enrolment->id], ['class' => 'btn btn-default review-cancel']);
            ?>
		<?php else :?>
			<?= Html::a('Cancel', ['lesson/index', '#' => 'group'], ['class' => 'btn btn-default review-cancel']);
            ?>
		<?php endif; ?>
	</div>
</div>