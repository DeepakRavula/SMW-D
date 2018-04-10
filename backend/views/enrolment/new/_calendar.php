<?php

use yii\helpers\Url;
use common\models\Location;
use common\models\LocationAvailability;

?>

<?= $this->render('/lesson/_color-code');?>
<div id="reverse-enrolment-calendar"></div>
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
<script type="text/javascript">
 $(document).ready(function() {
     var calendar = {
         refresh : function(){
             var events, availableHours;
             var teacherId = $('#course-teacherid').val();
             if(teacherId!==null && teacherId!=="")
             {
             var date = moment($('#course-startdate').val(), 'MMM D,YYYY', true).format('YYYY-MM-DD');
 			if (! moment(date).isValid()) {
                 var date = moment($('#course-startdate').val(), 'MMM D,YYYY hh:mm A', true).format('YYYY-MM-DD');
             }
	     $('#courseschedule-day').val(moment(date).format('dddd'));
 			$.ajax({
 				url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
 				type: 'get',
 				dataType: "json",
 				success: function (response)
 				{
 					events = response.events;
 					availableHours = response.availableHours;
 					enrolment.refreshCalendar(availableHours, events, date);
 				}
 			});
            }
         }
     };
 	var enrolment = {
         refreshCalendar : function(availableHours, events, date){
             $('#reverse-enrolment-calendar').fullCalendar('destroy');
             $('#reverse-enrolment-calendar').fullCalendar({
             	schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                 defaultDate: date,
		    	 firstDay : 1,
		        nowIndicator: true,
                 header: {
                     left: 'prev,next today',
                     center: 'title',
                     right:'',
                     },
                 allDaySlot: false,
 				height:450,
                 slotDuration: '00:15:00',
                 titleFormat: 'DD-MMM-YYYY, dddd',
                 defaultView: 'agendaWeek',
                 minTime: "<?php echo $minTime; ?>",
                 maxTime: "<?php echo $maxTime; ?>",
                selectConstraint: {
                    start: '00:01', // a start time (10am in this example)
                    end: '24:00', // an end time (6pm in this example)
                    dow: [ 1, 2, 3, 4, 5, 6, 0 ]
                },
                eventConstraint: {
                    start: '00:01', // a start time (10am in this example)
                    end: '24:00', // an end time (6pm in this example)
                    dow: [ 1, 2, 3, 4, 5, 6, 0 ]
                },
                 businessHours: availableHours,
                 overlapEvent: false,
                 overlapEventsSeparate: true,
                 events: events,
                 select: function (start, end, allDay) {
                     $('#course-startdate').val(moment(start).format('MMM D,YYYY hh:mm A'));
                     $('#courseschedule-fromtime').val(moment(start).format('hh:mm A'));
                     $('#reverse-enrolment-calendar').fullCalendar('removeEvents', 'newEnrolment');
 					$('#courseschedule-day').val(moment(start).format('dddd'));
 					var endtime = start.clone();
                 	var durationMinutes = moment.duration($('#courseschedule-duration').val()).asMinutes();
                 	moment(endtime.add(durationMinutes, 'minutes'));
                     $('#reverse-enrolment-calendar').fullCalendar('renderEvent',
                         {
                             id: 'newEnrolment',
                             start: start,
                             end: endtime,
                             allDay: false
                         },
                     true // make the event "stick"
                     );
                     $('#reverse-enrolment-calendar').fullCalendar('unselect');
                 },
                 eventAfterAllRender: function (view) {
                     $('.fc-short').removeClass('fc-short');
                 },
                 selectable: true,
                 selectHelper: true,
             });
         }
     };
 	$(document).on('change', '#course-startdate', function () {
 		calendar.refresh();
 	});
 	$(document).on('change', '#course-teacherid', function() {
 		$('#courseschedule-day').val('');
 		calendar.refresh();
 		return false;
 	});
 });
 </script>
