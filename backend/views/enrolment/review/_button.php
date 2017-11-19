<?php 
use common\models\Program;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php $url = Url::to(['confirm', 'courseId' => $courseModel->id]);?>
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
		<?= Html::a('Cancel', ['student/view', 'id' => $courseModel->enrolment->studentId], ['class' => 'btn btn-default']);
		?>
	</div>
</div>