<?php

use common\models\LocationAvailability;
use yii\helpers\Json;
?>
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
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<script>
    $(document).ready(function () {
		$('#new-enrolment-calendar').fullCalendar({
			defaultDate: moment(date, 'DD-MM-YYYY', true).format('YYYY-MM-DD'),
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
			businessHours: "<?php echo Json::encode($availableHours); ?>",
			overlapEvent: false,
			overlapEventsSeparate: true,
			events: "<?php echo Json::encode($events); ?>",
			select: function (start, end, allDay) {
				$('#extra-lesson-date').val(moment(start).format('YYYY-MM-DD hh:mm A'));
				$('#new-enrolment-calendar').fullCalendar('removeEvents', 'newEnrolment');
				var endtime = start.clone();
				var differenceInMinute = moment(end).minute() - moment(start).minute();
				if (differenceInMinute === 15) {
					moment(endtime.add(30, 'minutes'));
				} else {
					endtime = end;
				}
				var duration = moment.utc(moment(endtime, "HH:mm:ss").diff(moment(start, "HH:mm:ss"))).format("HH:mm:ss");
				$('#lesson-duration').val(duration);
				$('#lesson-calendar').fullCalendar('renderEvent',
					{
						id: 'newEnrolment',
						start: start,
						end: endtime,
						allDay: false
					},
				true // make the event "stick"
				);
				$('#new-enrolment-calendar').fullCalendar('unselect');
			},
			eventAfterAllRender: function (view) {
				$('.fc-short').removeClass('fc-short');
			},
			selectable: true,
			selectHelper: true,
		});
    });
</script>