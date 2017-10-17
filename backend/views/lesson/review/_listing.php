<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use common\components\gridView\AdminLteGridView;
use kartik\datetime\DateTimePickerAsset;
DateTimePickerAsset::register($this);
?>

<?php

//if ($conflictedLessonIdsCount > 0) {
//		$hasConflict = true;
//	}
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
		'id' => 'review-lesson-listing',
		'timeout' => 6000,
	]) ?>
<?=
AdminLteGridView::widget([
	'dataProvider' => $lessonDataProvider,
	'columns' => $columns,
	'emptyText' => 'No conflicts here! You are ready to confirm!',
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
	

<?php
Modal::begin([
	'header' => '<h4 class="m-0">Edit Lesson</h4>',
	'id'=>'review-lesson-modal',
]); ?>
<div id="review-lesson-content"></div>
<?php Modal::end();?>		
<script>
	var review = {
            var params = $.param({
                        'courseId': '<?php echo !empty($courseId) ? $courseId : null; ?>'
                });
		onEditableGridSuccess: function () {
			$.ajax({
				url: "<?php echo Url::to(['lesson/fetch-conflict']); ?>?" + params,
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
                        var courseId = '<?= !empty($courseId)  ? $courseId : null; ?>';
			var vacationId = '<?= !empty($vacationId)  ? $vacationId : null; ?>';
			var startDate = '<?= !empty($rescheduleBeginDate)  ? $rescheduleBeginDate : null; ?>';
			var endDate = '<?= !empty($rescheduleEndDate)  ? $rescheduleEndDate : null; ?>';
			if(vacationId) {
				var params = $.param({
					'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0),
					'Vacation[id]': vacationId, 'courseId': courseId
				});	
			} else if(startDate && endDate) {
				var params = $.param({
					'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0),
					'Course[startDate]' : startDate, 'Course[endDate]' : endDate, 'courseId': courseId
				});
			} else {
				var params = $.param({
					'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0), 'courseId': courseId
				});
			}
			var url = "<?php echo Url::to(['lesson/review']); ?>?" + params;
			$.pjax.reload({url: url, container: "#review-lesson-listing", replace: false, timeout: 4000});  //Reload GridView
		});
		
		$(document).on('click', '#lesson-review-cancel', function () {
            $('#review-lesson-modal').modal('hide');
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
                        $('#review-lesson-content').html(response.data);
                        $('#review-lesson-modal').modal('show');
                    }
                }
            });
			return false;
        });
		$(document).on('click','#lesson-review-apply, #lesson-review-apply-all',function() {
			$('#lesson-applycontext').val($(this).val());
            $('#spinner').show();
		});
		$(document).on('beforeSubmit', '#lesson-review-form', function (e) {
			 e.preventDefault();
			var lessonId = $('#lesson-id').val();
			var showAllReviewLessons = $('#lessonsearch-showallreviewlessons').is(":checked");
			var vacationId = '<?= !empty($vacationId) ? $vacationId : null; ?>';
			var params = $.param({'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0),
				'Vacation[id]': vacationId, 'courseId': '<?php echo !empty($courseId) ? $courseId : null; ?>'
			});
			var url = "<?php echo Url::to(['lesson/review']); ?>?" + params;
			$.ajax({
                url: '<?= Url::to(['lesson/update-field']); ?>?id=' + lessonId,
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#spinner').hide();
						$.pjax.reload({url: url, container: "#review-lesson-listing", replace: false, timeout: 4000, async:false});
						if($('#review-lesson-summary').length !== 0) {
							$.pjax.reload({url: url, container: "#review-lesson-summary", replace: false, timeout: 6000, async:false});
						}
                		review.onEditableGridSuccess();
                        $('#review-lesson-modal').modal('hide');
                    } else {
                        $('#spinner').hide();
				 		$('#lesson-review-form').yiiActiveForm('updateMessages',
					   		response.errors	, true);
					}
                }
            });
			return false;
		});	
	});
</script>
