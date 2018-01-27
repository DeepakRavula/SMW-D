<?php

use yii\helpers\Url;
use common\models\LocationAvailability;

require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';

?>
<?= $this->render('/lesson/_color-code');?>
<div id="enrolment-calendar">
    <div id="private-enrolment-spinner" class="spinner m-t-25" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
</div>
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
             $('#courseschedule-day').val(moment(date).format('dddd'));
 			if (! moment(date).isValid()) {
                 var date = moment($('#course-startdate').val(), 'YYYY-MM-DD hh:mm A', true).format('YYYY-MM-DD');
             }
 			$('#enrolment-edit-modal .modal-dialog').css({'width': '1000px'});
 			$.ajax({
 				url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
 				type: 'get',
 				dataType: "json",
 				success: function (response)
 				{
 					events = response.events;
 					availableHours = response.availableHours;
                                        $('#private-enrolment-spinner').hide();
 					enrolment.refreshCalendar(availableHours, events, date);
 				}
 			});
            }
         }
     };
 	var enrolment = {
         refreshCalendar : function(availableHours, events, date){
             $('#enrolment-calendar').fullCalendar('destroy');
             $('#enrolment-calendar').fullCalendar({
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
 				height:500,
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
                     $('#enrolment-calendar').fullCalendar('removeEvents', 'newEnrolment');
 					$('#courseschedule-day').val(moment(start).format('dddd'));
 					var endtime = start.clone();
                 	var durationMinutes = moment.duration($('#courseschedule-duration').val()).asMinutes();
                 	moment(endtime.add(durationMinutes, 'minutes'));
                     $('#enrolment-calendar').fullCalendar('renderEvent',
                         {
                             id: 'newEnrolment',
                             start: start,
                             end: endtime,
                             allDay: false
                         },
                     true // make the event "stick"
                     );
                     $('#enrolment-calendar').fullCalendar('unselect');
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
