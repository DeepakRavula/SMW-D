<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use common\components\gridView\AdminLteGridView;
use common\models\LocationAvailability;

use kartik\datetime\DateTimePickerAsset;
DateTimePickerAsset::register($this);
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';
$this->title = 'Review Lessons';
$this->params['show-all'] = $this->render('review/_show-all', [
	'searchModel' => $searchModel
]);
?>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<div class="row">
	<div class="col-md-6">
		<?=
		$this->render('review/_details', [
			'courseModel' => $courseModel,
		]);
		?>
		<?php if(empty($rescheduleBeginDate)) : ?>
			<?=
			$this->render('review/_summary', [
				'holidayConflictedLessonIds' => $holidayConflictedLessonIds,
				'unscheduledLessonCount' => $unscheduledLessonCount,
				'lessonCount' => $lessonCount,
				'conflictedLessonIdsCount' => $conflictedLessonIdsCount,
			]);
			?>
		<?php endif; ?>
	</div>
	<div class="col-md-6">
		<?=
		$this->render('review/_teacher-availability', [
			'courseModel' => $courseModel,
		]);
		?>
	</div>
</div>

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
	
<?= $this->render('review/_button', [
	'hasConflict' => $hasConflict,
	'rescheduleBeginDate' => $rescheduleBeginDate,
    'rescheduleEndDate' => $rescheduleEndDate,
	'courseId' => $courseId,
	'courseModel' => $courseModel,
	'enrolmentType' => $enrolmentType,
	
]); ?>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Edit Lesson</h4>',
	'id'=>'review-lesson-modal',
]); ?>
<div id="review-lesson-content"></div>
<?php Modal::end();?>		
<?php
$locationId = Yii::$app->session->get('location_id');
$minLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['fromTime' => SORT_ASC])
    ->one();
$maxLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['toTime' => SORT_DESC])
    ->one();
$from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
$to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
$teacherId=$courseModel->teacher->id;
?>
<script>
	var review = {
		onEditableGridSuccess: function () {
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
                 var calendar = {
            load: function (events, availableHours) {
                //var teacherId = $('#lesson-teacherid').val();
                //var params = $.param({teacherId: teacherId});
                $('#lesson-edit-calendar').fullCalendar('destroy');
                $('#lesson-edit-calendar').fullCalendar({
                    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'agendaWeek'
                    },
                    allDaySlot: false,
                    slotDuration: '00:15:00',
                    titleFormat: 'DD-MMM-YYYY, dddd',
                    defaultView: 'agendaWeek',
                    minTime: "<?php echo $from_time; ?>",
                    maxTime: "<?php echo $to_time; ?>",
                    selectConstraint: 'businessHours',
                    eventConstraint: 'businessHours',
                    businessHours: availableHours,
                    overlapEvent: false,
                    overlapEventsSeparate: true,
                    events: events,
                    select: function (start, end, allDay) {
                        $('#lesson-edit-calendar').fullCalendar('removeEvents', 'newEnrolment');
                        var duration = $('#course-duration').val();
                        var endtime = start.clone();
                        var durationMinutes = moment.duration(duration).asMinutes();
                        moment(endtime.add(durationMinutes, 'minutes'));

                        $('#lesson-edit-calendar').fullCalendar('renderEvent',
                                {
                                    id: 'newEnrolment',
                                    start: start,
                                    end: endtime,
                                    allDay: false
                                },
                                true // make the event "stick"
                                );
                        $('#lesson-edit-calendar').fullCalendar('unselect');
                    },
                    selectable: true,
                    selectHelper: true,
                    eventAfterAllRender: function () {
                        $('.fc-short').removeClass('fc-short');
                    },
                });
            }
        };
        var refreshcalendar = {
            refresh: function () {
                var events, availableHours;
                var teacherId = <?=$teacherId?>;
                        $.ajax({
                        url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
                        type: 'get',
                        dataType: "json",
                        success: function (response)
                        {
                            alert(response.events);
                            events = response.events;
                            availableHours = response.availableHours;
                             calendar.load(events,availableHours);
                        }
                    });
            }
        };
		$("#lessonsearch-showallreviewlessons").on("change", function () {
			var showAllReviewLessons = $(this).is(":checked");
			var startDate = '<?= $rescheduleBeginDate; ?>';
			var endDate = '<?= $rescheduleEndDate; ?>';
			if(startDate && endDate) {
				var params = $.param({
					'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0),
					'Course[startDate]' : startDate, 'Course[endDate]' : endDate
				});
			} else {
				var params = $.param({
					'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0),
				});
			}
			var url = "<?php echo Url::to(['lesson/review', 'courseId' => $courseModel->id]); ?>?" + params;
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
                         refreshcalendar.refresh();
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
			var params = $.param({'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0)});
			var url = "<?php echo Url::to(['lesson/review', 'courseId' => $courseModel->id]); ?>?" + params;
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
