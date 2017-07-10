<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use common\models\Program;
use yii\bootstrap\Modal;



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
if ($conflictedLessonIdsCount > 0) {
		$hasConflict = true;
	}
?>
<?php
$columns = [
		[
		'label' => 'Date/Time',
		'attribute' => 'date',
		'format' => 'datetime',
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column'],
	],
		[
		'attribute' => 'duration',
		'value' => function ($model, $key, $index, $widget) {
			return (new \DateTime($model->duration))->format('H:i');
		},
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column'],
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
	[
		'class' => 'yii\grid\ActionColumn',
		'template' => '{edit}',
		'buttons' => [
			'edit' => function  ($url, $model) {
				return  Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>','#', [
					'id' => 'edit-button-' . $model->id,
					'class' => 'review-lesson-edit-button m-l-20'
				]);
			},
		],
	],
];
?>
	<?php yii\widgets\Pjax::begin([
		'id' => 'new-enrolment-review-lesson-listing'
	]) ?>
<?=
\yii\grid\GridView::widget([
	'dataProvider' => $lessonDataProvider,
	'columns' => $columns,
	'emptyText' => 'No conflicts here! You are ready to confirm!',
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div style="text-align: center">
	<strong>Unscheduled Lesson(s) due to holiday conflict:</strong> <?= count($holidayConflictedLessonIds);?><br>
	<strong>Scheduled Lessons:</strong> <?= $lessonCount - (count($holidayConflictedLessonIds) + $conflictedLessonIdsCount);?><br>
	<strong>Conflicted Lesson(s):</strong> <?= $conflictedLessonIdsCount;?><br>
	<strong>Total Lessons:</strong> <?= $lessonCount;?><br>
</div>

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
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Edit Lesson</h4>',
	'id'=>'new-enrolment-preview-modal',
]); ?>
<div id="new-enrolment-preview-content"></div>
<?php Modal::end();?>	
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
		$(document).on('click', '#lesson-review-cancel', function () {
            $('#new-enrolment-preview-modal').modal('hide');
			return false;
		});
		$(document).on('click', '.review-lesson-edit-button', function () {
            $.ajax({
                url: '<?= Url::to(['lesson/update-field']); ?>?id=' + $(this).parent().parent().data('key'),
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#new-enrolment-preview-content').html(response.data);
                        $('#new-enrolment-preview-modal').modal('show');
                    }
                }
            });
			return false;
        });
		$(document).on('beforeSubmit', '#lesson-review-form', function (e) {
			var lessonId = $('#lesson-id').val();
			$.ajax({
                url: '<?= Url::to(['lesson/update-field']); ?>?id=' + lessonId,
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
						$.pjax.reload({container: "#new-enrolment-review-lesson-listing", replace: false, timeout: 4000});
                        $('#new-enrolment-preview-modal').modal('hide');
                    } else {
				 		$('#lesson-review-form').yiiActiveForm('updateMessages',
					   		response.errors	, true);
					}
                }
            });
			return false;
		});	
	});
</script>