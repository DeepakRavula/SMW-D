<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Location;
use common\models\LocationAvailability;
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

<?php echo $this->render('_time-voucher-content',['searchModel'=>$searchModel,'teacherLessonDataProvider' => $teacherLessonDataProvider]); ?>
 <?php Modal::begin([
        'header' => '<h4 class="m-0">Edit</h4>',
        'id' => 'lesson-modal',
    ]); ?>
<div id="lesson-content"></div>
 <?php  Modal::end(); ?>
<?php
    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
    $minLocationAvailability = LocationAvailability::find()
        ->location($locationId)
        ->locationaAvailabilityHours()
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->location($locationId)
        ->locationaAvailabilityHours()
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    if (empty($minLocationAvailability)) {
        $minTime = LocationAvailability::DEFAULT_FROM_TIME;
    } else {
        $minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    }
    if (empty($maxLocationAvailability)) {
        $maxTime = LocationAvailability::DEFAULT_TO_TIME;
    } else {
        $maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
    }
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
			var params = $.param({ id: lessonId });
			lesson.update(params);
            
            return false;
        });
        $("#lessonsearch-summarisereport").on("change", function() {
        var summariesOnly = $(this).is(":checked");
        var dateRange = $('#lessonsearch-daterange').val();
        var params = $.param({ 'LessonSearch[dateRange]': dateRange,'LessonSearch[summariseReport]':summariesOnly | 0 });
        var url = '<?php echo Url::to(['user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $model->id]); ?>&' + params;
        $.pjax.reload({url:url,container:"#teacher-lesson-grid",replace:false,  timeout: 4000});  //Reload GridView
		var printUrl = '<?= Url::to(['print/teacher-lessons', 'id' => $model->id]); ?>&' + params;
		 $('#print-btn').attr('href', printUrl);
    });
        $("#teacher-lesson-search-form").on("submit", function () {
            var summariesOnly = $(this).is(":checked");
        var dateRange = $('#lessonsearch-daterange').val();
        var params = $.param({ 'LessonSearch[dateRange]': dateRange,'LessonSearch[summariseReport]': summariesOnly | 0 });
            $.pjax.reload({container: "#teacher-lesson-grid", replace: false, timeout: 6000, data: $(this).serialize()});
            var url = '<?= Url::to(['print/teacher-lessons', 'id' => $model->id]); ?>&' + params;
            $('#print-btn').attr('href', url);
            return false;
        });
    });
</script>
