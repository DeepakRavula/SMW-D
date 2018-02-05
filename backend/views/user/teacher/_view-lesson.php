<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use common\models\LocationAvailability;
use common\models\Lesson;
use common\models\Qualification;
use kartik\daterange\DateRangePicker;

?>
<?php $this->render('/lesson/_color-code'); ?>
<div class="col-md-12">
	<?php
    $form = ActiveForm::begin([
            'id' => 'teacher-lesson-search-form',
    ]);
    ?>
    <div class="row">
        <div class="col-md-3 form-group">
            <?php
            echo DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'dateRange',
                'convertFormat' => true,
                'initRangeExpr' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'ranges' => [
                        Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                        Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                    ],
                    'locale' => [
                        'format' => 'M d,Y',
                    ],
                    'opens' => 'right',
                ],
            ]);

            ?>
        </div>
        <div class="col-md-1 form-group">
            <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'lesson-search', 'class' => 'btn btn-primary']) ?>
        </div>
        <div class="col-md-1 form-group">
            <?= Html::a('<i class="fa fa-print"></i> Print', ['print/teacher-lessons', 'id' => $model->id], ['id' => 'print-btn', 'class' => 'btn btn-default m-r-10', 'target' => '_blank']) ?>
        </div>
         <div class="pull-right checkbox">
           <?= $form->field($searchModel, 'summariseReport')->checkbox(['data-pjax' => true]); ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
if (!$searchModel->summariseReport) {
$columns = [
        [
        'value' => function ($data) {
            if (! empty($data->date)) {
                $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                return $lessonDate->format('l, F jS, Y');
            }

            return null;
        },
        'group' => true,
        'groupedRow' => true,
        'groupFooter' => function ($model, $key, $index, $widget) {
            return [
                'mergeColumns' => [[1, 3]],
                'content' => [
                    4 => GridView::F_SUM,
                ],
                'contentFormats' => [
                    4 => ['format' => 'number', 'decimals' => 2],
                ],
                'contentOptions' => [
                    4 => ['style' => 'text-align:right'],
                ],
            'options'=>['style'=>'font-weight:bold;']
            ];
        }
    ],
        [
        'label' => 'Time',
        'width' => '250px',
        'value' => function ($data) {
            return !empty($data->date) ? Yii::$app->formatter->asTime($data->date) : null;
        },
    ],
        [
        'label' => 'Program',
        'width' => '250px',
        'value' => function ($data) {
            return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
        },
    ],
        [
        'label' => 'Student',
        'value' => function ($data) {
            $student = ' - ';
            if ($data->course->program->isPrivate()) {
                $student = !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
            }
            return $student;
        },
    ],
        [
        'label' => 'Duration(hrs)',
        'value' => function ($data) {
            return $data->getDuration();
        },
        'contentOptions' => ['class' => 'text-right'],
            'hAlign'=>'right',
            'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
    ],
];
        } else {

        $columns = [
                [
        'label' => 'Date',
        'value' => function ($data) {
            if (! empty($data->date)) {
                $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                return $lessonDate->format('l, F jS, Y');
            }

            return null;
        },
            
    ],
        		[
			'label' => 'Duration(hrs)',
			'value' => function ($data){
				$locationId = Yii::$app->session->get('location_id');
				$lessons = Lesson::find()
					->location($locationId)
					->notDeleted()
					->andWhere(['status' => [Lesson::STATUS_COMPLETED,Lesson::STATUS_SCHEDULED]])
					->andWhere(['DATE(date)' => (new \DateTime($data->date))->format('Y-m-d'), 'lesson.teacherId' => $data->teacherId])
					->all();
				$totalDuration = 0;
				foreach($lessons as $lesson) {
					$duration		 = \DateTime::createFromFormat('H:i:s', $lesson->fullDuration);
					$hours			 = $duration->format('H');
					$minutes		 = $duration->format('i');
					$lessonDuration	 = $hours + ($minutes / 60);
					$totalDuration += $lessonDuration;
				}
				return $totalDuration;
			},
			'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
		],
            ];
        }
?>
<?=
GridView::widget([
    'dataProvider' => $teacherLessonDataProvider,
        'summary' => false,
        'emptyText' => false,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'pjax' => true,
    'showPageSummary' => true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'teacher-lesson-grid',
        ],
    ],
    'columns' => $columns,
]);
?>
 <?php Modal::begin([
        'header' => '<h4 class="m-0">Edit</h4>',
        'id' => 'lesson-modal',
    ]); ?>
<div id="lesson-content"></div>
 <?php  Modal::end(); ?>
<?php
$locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
$minLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['fromTime' => SORT_ASC])
    ->one();
$maxLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['toTime' => SORT_DESC])
    ->one();
$minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
$maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>

<script>
    $(document).ready(function () {
	var calendar = {
		load : function(events,availableHours) {
		    $('#teacher-lesson').fullCalendar('destroy');
            $('#teacher-lesson').fullCalendar({
            	schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
				firstDay : 1,
	            nowIndicator: true,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right:'',
                },
                allDaySlot: false,
                slotDuration: '00:15:00',
                titleFormat: 'DD-MMM-YYYY, dddd',
                defaultView: 'agendaWeek',
                minTime: "<?php echo $minTime; ?>",
                maxTime: "<?php echo $maxTime; ?>",
                overlapEvent: false,
                overlapEventsSeparate: true,
                businessHours: availableHours,
                events: events,
                select: function (start, end, allDay) {
                    $('#lesson-date1').val(moment(start).format('DD-MM-YYYY hh:mm A'));
                    $('#teacher-lesson').fullCalendar('removeEvents', 'newEnrolment');
					var duration = $('#lesson-duration').val();
					var endtime = start.clone();
					var durationMinutes = moment.duration(duration).asMinutes();
					moment(endtime.add(durationMinutes, 'minutes'));

                    $('#teacher-lesson').fullCalendar('renderEvent',
                        {
                            id: 'newEnrolment',
                            start: start,
                            end: endtime,
                            allDay: false
                        },
                    true // make the event "stick"
                    );
                    $('#teacher-lesson').fullCalendar('unselect');
                },
                selectable: true,
                selectHelper: true,
            });
		}
	};
var refreshcalendar = {
        refresh : function(){
            var events, availableHours;
            var teacherId = $('#lesson-teacherid').val();
                $.ajax({
                    url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
                    type: 'get',
                    dataType: "json",
                    success: function (response)
                    {
                        events = response.events;
                        availableHours = response.availableHours;
                        calendar.load(events,availableHours);
                    }
                });
            }
        };

		$(document).on('click', '.lesson-cancel', function () {
            $('#lesson-modal').modal('hide');
			return false;
		});
		$(document).on('change', '#lesson-teacherid', function () {
            refreshcalendar.refresh();
			return false;
		});
		$(document).on('click', '#teacher-lesson-grid  tbody > tr', function () {
            var lessonId = $(this).data('key');
			var params = $.param({ lessonId: lessonId });
            $.ajax({
                url    : '<?= Url::to(['user/edit-lesson']);?>?' + params,
                type   : 'get',
                dataType: "json",
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#lesson-content').html(response.data);
                		$('#lesson-modal .modal-dialog').css({'width': '1000px'});
                         refreshcalendar.refresh();
                        $('#lesson-modal').modal('show');

                    }
                }
            });
            return false;
        });
        $("#lessonsearch-summarisereport").on("change", function() {
        var summariesOnly = $(this).is(":checked");
        var dateRange = $('#lessonsearch-daterange').val();
        var params = $.param({ 'LessonSearch[dateRange]': dateRange,'LessonSearch[summariseReport]':summariesOnly });
        var url = '<?php echo Url::to(['user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $model->id]); ?>&' + params;
        $.pjax.reload({url:url,container:"#teacher-lesson-grid",replace:false,  timeout: 4000});  //Reload GridView
		var printUrl = '<?= Url::to(['print/teacher-lessons', 'id' => $model->id]); ?>&' + params;
		 $('#print-btn').attr('href', printUrl);
    });
        $("#teacher-lesson-search-form").on("submit", function () {
            var summariesOnly = $(this).is(":checked");
        var dateRange = $('#lessonsearch-daterange').val();
        var params = $.param({ 'LessonSearch[dateRange]': dateRange,'LessonSearch[summariseReport]': summariesOnly });
            $.pjax.reload({container: "#teacher-lesson-grid", replace: false, timeout: 6000, data: $(this).serialize()});
            var url = '<?= Url::to(['print/teacher-lessons', 'id' => $model->id]); ?>&' + params;
            $('#print-btn').attr('href', url);
            return false;
        });
    });
</script>
