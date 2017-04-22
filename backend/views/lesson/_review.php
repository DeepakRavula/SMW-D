<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Review Lessons';
?>
<style>
	.e1Div{
		top: -46px;
		right: 0px;
	}
</style>
<?php $form = ActiveForm::begin(); ?>
<div class="pull-right  m-r-20">
	<div class="schedule-index">
		<div class="e1Div">
<?= $form->field($searchModel, 'showAllReviewLessons')->checkbox(['data-pjax' => true]); ?>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
<?=
$this->render('review/details', [
	'courseModel' => $courseModel,
]);
?>
<?=
$this->render('review/teacher-availability', [
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
		'class' => 'kartik\grid\EditableColumn',
		'attribute' => 'date',
		'format' => 'datetime',
		'refreshGrid' => true,
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column'],
		'editableOptions' => function ($model, $key, $index) {
			return [
				'header' => 'Lesson Date',
				'size' => 'md',
				'inputType' => \kartik\editable\Editable::INPUT_WIDGET,
				'widgetClass' => '\bootui\datetimepicker\DateTimepicker',
				'options' => [
					'format' => 'YYYY-MM-DD hh:mm A',
					'stepping' => 15,
				],
				'formOptions' => ['action' => Url::to(['lesson/update-field'])],
				'pluginEvents' => [
					'editableError' => 'review.onEditableError',
					'editableSuccess' => 'review.onEditableGridSuccess',
				],
			];
		},
	],
		[
		'class' => 'kartik\grid\EditableColumn',
		'attribute' => 'duration',
		'refreshGrid' => true,
		'value' => function ($model, $key, $index, $widget) {
			return (new \DateTime($model->duration))->format('H:i');
		},
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column'],
		'editableOptions' => function ($model, $key, $index) {
			return [
				'header' => 'Lesson Duration',
				'size' => 'md',
				'inputType' => \kartik\editable\Editable::INPUT_WIDGET,
				'widgetClass' => 'bootui\datetimepicker\Timepicker',
				'options' => [
					'format' => 'HH:mm',
					'stepping' => 15,
				],
				'formOptions' => ['action' => Url::to(['lesson/update-field'])],
				'pluginEvents' => [
					'editableSuccess' => 'review.onEditableGridSuccess',
				],
			];
		},
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
<?= $this->render('review/button', [
	'vacationId' => $vacationId,
	'hasConflict' => $hasConflict,
	'rescheduleBeginDate' => $rescheduleBeginDate,
	'endDate' => $endDate,
	'courseId' => $courseId,
	'courseModel' => $courseModel	
]); ?>
<script>
	var review = {
		onEditableError: function (event, val, form, data) {
			$(form).find('.form-group').addClass('has-error');
			$(form).find('.help-block').text(data.message);
		},
		onEditableGridSuccess: function (event, val, form, data) {
			$.ajax({
				url: "<?php echo Url::to(['lesson/fetch-conflict', 'courseId' => $courseId]); ?>",
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
		$("#lessonsearch-showallreviewlessons").on("change", function () {
			var showAllReviewLessons = $(this).is(":checked");
			var vacationId = '<?= $vacationId; ?>';
			var vacationType = '<?= $vacationType; ?>';
			var params = $.param({'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0),
				'Vacation[id]': vacationId, 'Vacation[type]': vacationType
			});
			var url = "<?php echo Url::to(['lesson/review', 'courseId' => $courseModel->id]); ?>?" + params;
			$.pjax.reload({url: url, container: "#review-lesson-listing", replace: false, timeout: 4000});  //Reload GridView
		});
	});
</script>