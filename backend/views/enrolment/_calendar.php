<?php
use common\models\Location;
use common\models\TeacherAvailability;
use common\models\Lesson;
use common\models\Program;
use yii\helpers\Json;
use yii\helpers\Url;
?>
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<?php
	$locationId			 = Yii::$app->session->get('location_id');
	$location = Location::findOne(['id' => $locationId]);
	$from_time = (new \DateTime($location->from_time))->format('H:i:s');
	$to_time = (new \DateTime($location->to_time))->format('H:i:s');
?>
<div class="calendar">
<div id='calendar' class="p-10"></div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  $('#calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'agendaWeek,agendaDay'
    },
    slotDuration: '00:15:00',
    titleFormat: 'DD-MMM-YYYY, dddd',
    defaultView: 'agendaWeek',
    minTime: "<?php echo $from_time; ?>",
    maxTime: "<?php echo $to_time; ?>",
    selectConstraint: 'businessHours',
    eventConstraint: 'businessHours',
    businessHours: <?php echo Json::encode($teacherDetails['availableHours']); ?>,
    allowCalEventOverlap: true,
    overlapEventsSeparate: true,
    events: <?php echo Json::encode($teacherDetails['events']); ?>,
    select: function(start, end, allDay) {
        $('#calendar').fullCalendar('removeEvents', 'newEnrolment');
        $('#course-day').val(moment(start).format('dddd'));
        $('#course-fromtime').val(moment(start).format('h:mm A'));
        $('#course-startdate').val(moment(start).format('DD-MM-YYYY'));
		$('#calendar').fullCalendar('renderEvent',
			{
				id : 'newEnrolment',
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
});
</script>