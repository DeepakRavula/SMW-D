<?php

use common\models\LocationAvailability;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="row-fluid">
	<div id="calendar" ></div>
</div>
 <div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary lesson-edit-save', 'name' => 'button']) ?>
	<?= Html::a('Cancel', '#', ['class' => 'btn btn-default lesson-edit-cancel']);
	?>
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
        $('#calendar').fullCalendar('destroy');
        $('#calendar').fullCalendar({
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
                $('#calendar').fullCalendar('removeEvents', 'newEnrolment');
                $('#lesson-date').val(moment(start).format('DD-MM-YYYY h:mm A'));
                var endtime = start.clone();
                var durationMinutes = moment.duration($('#course-duration').val()).asMinutes();
                moment(endtime.add(durationMinutes, 'minutes'));
                $('#calendar').fullCalendar('renderEvent',
                    {
                        id: 'newEnrolment',
                        start: start,
                        end: endtime,
                        allDay: false
                    },
                true // make the event "stick"
                );
                $('#calendar').fullCalendar('unselect');
            },
            eventAfterAllRender: function (view) {
                $('.fc-short').removeClass('fc-short');
            },
            selectable: true,
            selectHelper: true,
        });
    }
    $(document).ready(function () {
		$(document).on('click', '.lesson-edit-save', function (e) {
			$('#lesson-edit-modal').modal('hide');
			return false;
		});
		$(document).on('click', '.lesson-edit-cancel', function (e) {
			$('#lesson-edit-modal').modal('hide');
			return false;
		});
		$(document).on('click', '.lesson-edit-calendar', function (e) {
			$('#lesson-edit-modal').modal('show');
            $('#lesson-edit-modal .modal-dialog').css({'width': '1000px'});
			var events, availableHours;
			var teacherId = $('#lesson-teacherid').val();
			var date = $('#course-startdate').val();
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