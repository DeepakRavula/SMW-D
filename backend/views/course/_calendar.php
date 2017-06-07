<?php

use common\models\LocationAvailability;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;

?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
 <div class="row-fluid">
	<div id="group-course-calendar"> </div>
</div>
 <div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Apply'), ['class' => 'btn btn-primary group-course-apply', 'name' => 'button']) ?>
	<?= Html::a('Cancel', '#', ['class' => 'btn btn-default group-course-cancel']);
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

<script>
$(document).ready(function(){
	
	$(document).on('click', '.group-course-apply', function (e) {
		$('#group-course-calendar-modal').modal('hide');
		return false;
	});
	$(document).on('click', '.group-course-cancel', function (e) {
		$('#group-course-calendar-modal').modal('hide');
		return false;
	});
	$(document).on('click', '.group-course-calendar-icon', function() {
		$('#group-course-calendar-modal').modal('show');
        $('#group-course-calendar-modal .modal-dialog').css({'width': '1000px'});
		var date = moment(new Date()).format('DD-MM-YYYY');
	    renderCalendar(date);
	});
    $(document).on('change', '#course-teacherid', function () {
        var date = $('#group-course-calendar').fullCalendar('getDate');
        renderCalendar(date);
    });

    function renderCalendar(date) {
        var events, availableHours;
        var teacherId = $('#course-teacherid').val();
        $.ajax({
            url: '/admin/teacher-availability/availability-with-events?id=' + teacherId,
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                events = response.events;
                availableHours = response.availableHours;
                refreshCalendar(availableHours, events, date);
            }
        });
    }

    function refreshCalendar(availableHours, events, date) {
        $('#group-course-calendar').fullCalendar('destroy');
        $('#group-course-calendar').fullCalendar({
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
                $('#course-day').val(moment(start).day());
                $('#course-startdate').val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
                $('#course-fromtime').val(moment(start).format('HH:mm:ss'));
                $('#group-course-calendar').fullCalendar('removeEvents', 'newEnrolment');
				var endtime = start.clone();
                var durationMinutes = moment.duration($('#course-duration').val()).asMinutes();
                moment(endtime.add(durationMinutes, 'minutes'));
                $('#group-course-calendar').fullCalendar('renderEvent',
                    {
                        id: 'newEnrolment',
                        start: start,
                        end: endtime,
                        allDay: false
                    },
                true // make the event "stick"
                );
                $('#group-course-calendar').fullCalendar('unselect');
            },
            eventAfterAllRender: function (view) {
                $('.fc-short').removeClass('fc-short');
            },
            selectable: true,
            selectHelper: true,
        });
    }

    $('#group-course-form').on('beforeSubmit', function (e) {
        var courseDay = $('#course-day').val();
        if( ! courseDay) {
            $('#error-notification').html("Please choose a day in the calendar").fadeIn().delay(3000).fadeOut();
            $(window).scrollTop(0);
            return false;
        }
    });
});
</script>
