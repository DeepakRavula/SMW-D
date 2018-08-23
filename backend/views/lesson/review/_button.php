<?php 
use common\models\Program;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php if ($model->rescheduleBeginDate && $model->rescheduleEndDate): ?>
	<?php $url = Url::to(['confirm',
        'LessonConfirm[courseId]' => $model->courseId,
        'LessonConfirm[rescheduleBeginDate]' => $model->rescheduleBeginDate,
        'LessonConfirm[rescheduleEndDate]' => $model->rescheduleEndDate
    ]);?>
<?php elseif ($model->enrolmentType): ?>
	<?php $url = Url::to(['confirm',
        'LessonConfirm[courseId]' => $model->courseId,
        'LessonConfirm[enrolmentType]' => $model->enrolmentType
    ]);?>
<?php elseif ($model->enrolmentIds): ?>
    <?php $url = Url::to(['confirm',
        'LessonConfirm[enrolmentIds]' => $model->enrolmentIds,
        'LessonConfirm[changesFrom]' => $model->changesFrom,
        'LessonConfirm[teacherId]' => $model->teacherId
    ]);?>
<?php else : ?>
	<?php $url = Url::to(['confirm', 'LessonConfirm[courseId]' => $model->courseId]);?>
<?php endif; ?>
<div class="form-group">
	<div class="p-10 text-center">
		<?= Html::a('Confirm', $url, [
            'class' => 'btn btn-info',
            'id' => 'confirm-button',
            'disabled' => $hasConflict,
            'data' => [
                'method' => 'post'
            ]
        ]) ?>
    <?php if ($courseModel) : ?>
		<?php if ($courseModel->program->isPrivate() && !$model->enrolmentType) : ?>
			<?= Html::a('Cancel', ['student/view', 'id' => $courseModel->enrolment->studentId], ['class' => 'btn btn-default review-cancel']); ?>
		<?php elseif ($model->enrolmentType) : ?>
			<?= Html::a('Cancel', ['enrolment/cancel', 'id' => $courseModel->enrolment->id], ['class' => 'btn btn-default review-cancel']); ?>
		<?php else :?>
			<?= Html::a('Cancel', ['lesson/index', '#' => 'group'], ['class' => 'btn btn-default review-cancel']); ?>
        <?php endif; ?>
    <?php else :?>
        <?= Html::a('Cancel', ['enrolment/index'], ['class' => 'btn btn-default review-cancel']); ?>
    <?php endif; ?>
	</div>
</div>