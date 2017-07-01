<?php

use common\models\LocationAvailability;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="row-fluid">
	<div id="unschedule-lesson-calendar" ></div>
</div>
 <div class="form-group">
	 <?php $form = ActiveForm::begin([
       'id' => 'unschedule-lesson-form', 
        ]); ?>
	<?= $form->field($model, 'fromDate')->hiddenInput()->label(false);?>
	<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary unschedule-lesson-save', 'name' => 'button']) ?>
	<?= Html::a('Cancel', '#', ['class' => 'btn btn-default unschedule-lesson-cancel']);
	?>
	<?php ActiveForm::end(); ?>
	<div class="clearfix"></div>
</div>
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
?>
<script type="text/javascript">
    function refreshCalendar(availableHours, events) {
        $('#unschedule-lesson-calendar').fullCalendar('destroy');
        $('#unschedule-lesson-calendar').fullCalendar({
    		defaultDate: moment(new Date()).format('YYYY-MM-DD'),
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'agendaWeek'
            },
            allDaySlot: false,
			height:'auto',
            slotDuration: '00:15:00',
            titleFormat: 'DD-MMM-YYYY, dddd',
            defaultView: 'agendaWeek',
            minTime: "<?php echo $from_time; ?>",
            maxTime: "<?php echo $to_time; ?>",
            selectConstraint: 'businessHours',
            eventConstraint: 'businessHours',
            businessHours: availableHours,
            allowCalEventOverlap: true,
            overlapEventsSeparate: true,
            events: events,
            select: function (start, end, allDay) {
                $('#unschedule-lesson-calendar').fullCalendar('removeEvents', 'reschedule');
              	$('#user-fromdate').val(moment(start).format('DD-MM-YYYY h:mm A'));
                var endtime = start.clone();
				var lessonDuration = $('#unschedule-calendar').parent().prev('td').text();
                var durationMinutes = moment.duration(lessonDuration).asMinutes();
                moment(endtime.add(durationMinutes, 'minutes'));
                $('#unschedule-lesson-calendar').fullCalendar('renderEvent',
                    {
                        id: 'reschedule',
                        start: start,
                        end: endtime,
                        allDay: false
                    },
                true // make the event "stick"
                );
                $('#unschedule-lesson-calendar').fullCalendar('unselect');
            },
            eventAfterAllRender: function (view) {
                $('.fc-short').removeClass('fc-short');
            },
            selectable: true,
            selectHelper: true,
        });
    }
    $(document).ready(function () {
		$(document).on('click', '.unschedule-lesson-cancel, unschedule-lesson-save', function (e) {
			$('#unschedule-lesson-modal').modal('hide');
			return false;
		});
		$(document).on('click', '#unschedule-calendar', function (e) {
			$('#unschedule-lesson-modal').modal('show');
            $('#unschedule-lesson-modal .modal-dialog').css({'width': '1000px'});
			var events, availableHours;
			var teacherId = '<?= $model->id; ?>';
			$.ajax({
				url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
				type: 'get',
				dataType: "json",
				success: function (response)
				{
					events = response.events;
					availableHours = response.availableHours;
					refreshCalendar(availableHours, events);
				}
			});
		});
    });
</script>