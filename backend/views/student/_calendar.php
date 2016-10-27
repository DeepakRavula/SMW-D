<?php

use common\models\Location;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="col-md-4">
	<?php
	// Dependent Dropdown
	echo $form->field($model, 'teacherId')->widget(DepDrop::classname(),
		[
		'options' => ['id' => 'course-teacherid'],
		'pluginOptions' => [
			'depends' => ['course-programid'],
			'placeholder' => 'Select...',
			'url' => Url::to(['course/teachers']),
		]
	]);
	?>
</div>
<div class="col-md-4">
	<?= $form->field($model, 'day')->hiddenInput()->label(false) ?>
</div>
<div class="col-md-4">
<?= $form->field($model, 'fromTime')->hiddenInput()->label(false) ?>
</div>
<div class="clearfix"></div>
<div class="row-fluid">
	<div id="calendar" ></div>
</div>
<?php
$locationId	 = Yii::$app->session->get('location_id');
$location	 = Location::findOne(['id' => $locationId]);
$from_time	 = (new \DateTime($location->from_time))->format('H:i:s');
$to_time	 = (new \DateTime($location->to_time))->format('H:i:s');
?>
<script type="text/javascript">
    function refreshCalendar(availableHours, events) {
        $('#calendar').fullCalendar('destroy');
        $('#calendar').fullCalendar({
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
            allowCalEventOverlap: true,
            overlapEventsSeparate: true,
            events: events,
            select: function (start, end, allDay) {
                $('#calendar').fullCalendar('removeEvents', 'newEnrolment');
                $('#course-day').val(moment(start).format('dddd'));
                $('#course-fromtime').val(moment(start).format('h:mm A'));
                $('#course-startdate').val(moment(start).format('DD-MM-YYYY'));
                $('#calendar').fullCalendar('renderEvent',
                        {
                            id: 'newEnrolment',
                            start: start,
                            end: end,
                            allDay: false
                        },
                true // make the event "stick"
                        );
            },
            selectable: true,
            selectHelper: true,
        });
    }
    $(document).ready(function () {
        $(document).on('change', '#course-teacherid', function () {
            var events, availableHours;
            var teacherId = $('#course-teacherid').val();
            $.ajax({
                url: '/teacher-availability/availability-with-events?id=' + teacherId,
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