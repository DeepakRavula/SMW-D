<?php

use yii\helpers\Url;
use common\models\LocationAvailability;

?>

<?= $this->render('/lesson/_color-code');?>
<div id="reverse-enrolment-calendar"></div>
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
    $from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>
<script type="text/javascript">
 $(document).ready(function() {
     var calendar = {
         refresh : function(){
             var events, availableHours;
             var teacherId = $('#course-teacherid').val();
             if(teacherId!==null && teacherId!=="")
             {
             var date = moment($('#course-startdate').val(), 'DD-MM-YYYY', true).format('YYYY-MM-DD');
 			if (! moment(date).isValid()) {
                 var date = moment($('#course-startdate').val(), 'YYYY-MM-DD hh:mm A', true).format('YYYY-MM-DD');
             }
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
                 nowIndicator: true,
             	schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                 defaultDate: date,
                 header: {
                     left: 'prev,next today',
                     center: 'title',
                     right:'',
                     },
					 firstDay :1,
                 allDaySlot: false,
 				height:450,
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
                     $('#course-startdate').val(moment(start).format('DD-MM-YYYY hh:mm A'));
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
