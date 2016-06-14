<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Schedule';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schedule-index">
<div id='calendar'></div>
</div>
<script type="text/javascript">
$(document).ready(function() { 
  var date = new Date();
  var d = date.getDate();
  var m = date.getMonth();
  var y = date.getFullYear();
  
  $('#calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,resourceDay'
    },    
    defaultView: 'resourceDay',
    minTime: "<?php echo $from_time; ?>",
    maxTime: "<?php echo $to_time; ?>",
    slotDuration: "00:30:01",
    editable: true,
    droppable: true,
    resources:  <?php echo Json::encode($teacherAvailability); ?>,
    events: <?php echo Json::encode($events); ?>,
    // the 'ev' parameter is the mouse event rather than the resource 'event'
    // the ev.data is the resource column clicked upon
    selectable: true,
    selectHelper: true,
    select: function(start, end, ev) {
      console.log(start);
      console.log(end);
      console.log(ev.data); // resources
    },
    eventClick: function(event) {
       event.title = "CLICKED!";

       $('#calendar').fullCalendar('updateEvent', event);
    },
    eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
        
        updateEventTimes(event, dayDelta, minuteDelta, allDay, revertFunc)

       //$('#calendar').fullCalendar('updateEvent', event);
    }
  });
  
    function updateEventTimes(event, dayDelta, minuteDelta, allDay, revertFunc)
    {
        alert(
            event.title + " was moved " +
            dayDelta + " days and " +
            minuteDelta + " minutes."
        );
      //console.log(event);
      $.ajax({
        url: "<?php echo Url::to(['schedule/updateEvents']);?>",
        type: "POST",
        contentType: 'application/json',
        data: ({
          id: event.id,
          day: dayDelta,
          min: minuteDelta,
          allday: allDay
        }),
        success: function(data, textStatus) {
          if (!data)
          {
            console.log(data);
            return;
          }
          calendar.fullCalendar('updateEvent', event);
        },
        error: function() {
         // revertFunc();
        }
    });
    };
});
</script>