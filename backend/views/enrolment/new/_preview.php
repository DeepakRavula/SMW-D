<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use common\models\Program;

$this->title = 'New Enrolment';
?>
<div class="clearfix"></div>
<?=
$this->render('/lesson/review/_details', [
	'courseModel' => $courseModel,
]);
?>
<?php
$hasConflict = false;
foreach ($conflicts as $conflict) {
	if (!empty($conflict)) {
		$hasConflict = true;
		break;
	}
}
?>
<?php
$columns = [
		[
		'label' => 'Date/Time',
		'attribute' => 'date',
		'format' => 'datetime',
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['style' => 'width:150px;  min-width:360px;'],
	],
		[
		'attribute' => 'duration',
		'value' => function ($model, $key, $index, $widget) {
			return (new \DateTime($model->duration))->format('H:i');
		},
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['style' => 'width:100px;  min-width:360px;'],
	],
		[
		'label' => 'Conflict',
		'headerOptions' => ['class' => 'bg-light-gray'],
		'value' => function ($data) use ($conflicts) {
			if (!empty($conflicts[$data->id])) {
				return current($conflicts[$data->id]);
			}
		},
	],
];
?>
<?=
\kartik\grid\GridView::widget([
	'dataProvider' => $lessonDataProvider,
	'pjax' => true,
	'pjaxSettings' => [
		'neverTimeout' => true,
		'options' => [
			'id' => 'review-lesson-listing',
		],
	],
	'columns' => $columns,
	'emptyText' => 'No conflicts here! You are ready to confirm!',
]);
?>
<?=
	Html::a('Confirm', ['confirm', 'courseId' => $courseModel->id], [
		'class' => 'btn btn-primary',
		'id' => 'confirm-button',
		'disabled' => $hasConflict,
		'data' => [
			'method' => 'post',
		],
	])
	?>
	<?= Html::a('Cancel', ['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false], ['class' => 'btn btn-default']);
	?>
<script>
	var review = {
		onEditableError: function (event, val, form, data) {
			$(form).find('.form-group').addClass('has-error');
			$(form).find('.help-block').text(data.message);
		},
		onEditableGridSuccess: function (event, val, form, data) {
			$.ajax({
				url: "<?php echo Url::to(['lesson/fetch-conflict', 'courseId' => $courseModel->id]); ?>",
				type: "GET",
				dataType: "json",
				success: function (response)
				{
					if (response.hasConflict) {
						$("#confirm-button").attr("disabled", true);
						$('#confirm-button').bind('click', false);
					} else {
						$("#confirm-button").removeAttr('disabled');
						$('#confirm-button').unbind('click', false);
					}
				}
			});
			return true;
		}
	}
	$(document).ready(function () {
		if ($('#confirm-button').attr('disabled')) {
			$('#confirm-button').bind('click', false);
		}
	});
</script>