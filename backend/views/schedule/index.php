<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Json;

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
    editable: true,
    droppable: true,
    resources:  <?php echo Json::encode($teacherAvailability); ?>,
    //events: <?php echo Json::encode($events); ?>,
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
      console.log(event);
    },
    eventDrop: function (event, delta, revertFunc) {
      console.log(event);
    }
  });
  
  $('#calendar').fullCalendar( 'addEventSource',        
    function(start, end, status, callback) {
        // When requested, dynamically generate a
        // repeatable event for every monday.
        var events = [];
        //var monday = 1;
        var one_day = (24 * 60 * 60 * 1000);
        
        var student_events = <?php echo Json::encode($events); ?>;
        
        for (loop = start._d.getTime();
            loop <= end._d.getTime();
            loop = loop + one_day) {

            var column_date = new Date(loop);
            switch (column_date.getDay()) {
                case 0:
                    $.each(student_events, function(i, val)  { 
                        if (val.day == 7) {
                            // we're in sunday, create the event
                            events.push({
                                title: val.title,
                                start: new Date(column_date.setHours(val.start_hours, val.start_minutes)),
                                end: new Date(column_date.setHours(val.end_hours, val.end_minutes)),
                                allDay: false,
                                resources: val.resources
                            })
                        }
                    });
                    break;
                case 1:
                    $.each(student_events, function(i, val)  { 
                        if (val.day == 1) {
                            // we're in Monday, create the event
                            events.push({
                                title: val.title,
                                start: new Date(column_date.setHours(val.start_hours, val.start_minutes)),
                                end: new Date(column_date.setHours(val.end_hours, val.end_minutes)),
                                allDay: false,
                                resources: val.resources
                            })
                        }
                    });
                    break;
                case 2:
                    $.each(student_events, function(i, val)  { 
                        if (val.day == 2) {
                            // we're in Tuesday, create the event
                            events.push({
                                title: val.title,
                                start: new Date(column_date.setHours(val.start_hours, val.start_minutes)),
                                end: new Date(column_date.setHours(val.end_hours, val.end_minutes)),
                                allDay: false,
                                resources: val.resources
                            })
                        }
                    });
                    break;
                case 3:
                    $.each(student_events, function(i, val)  { 
                        if (val.day == 3) {
                            // we're in Wednesday, create the event
                            events.push({
                                title: val.title,
                                start: new Date(column_date.setHours(val.start_hours, val.start_minutes)),
                                end: new Date(column_date.setHours(val.end_hours, val.end_minutes)),
                                allDay: false,
                                resources: val.resources
                            })
                        }
                    });
                    break;
                case 4:
                    $.each(student_events, function(i, val)  { 
                        if (val.day == 4) {
                            // we're in Thursday, create the event
                            events.push({
                                title: val.title,
                                start: new Date(column_date.setHours(val.start_hours, val.start_minutes)),
                                end: new Date(column_date.setHours(val.end_hours, val.end_minutes)),
                                allDay: false,
                                resources: val.resources
                            })
                        }
                    });
                    break;
                case 5:
                    $.each(student_events, function(i, val)  { 
                        if (val.day == 5) {
                            // we're in Friday, create the event
                            events.push({
                                title: val.title,
                                start: new Date(column_date.setHours(val.start_hours, val.start_minutes)),
                                end: new Date(column_date.setHours(val.end_hours, val.end_minutes)),
                                allDay: false,
                                resources: val.resources
                            })
                        }
                    });
                    break;
                case  6:
                    $.each(student_events, function(i, val)  { 
                        if (val.day == 6) {
                            // we're in Saturday, create the event
                            events.push({
                                title: val.title,
                                start: new Date(column_date.setHours(val.start_hours, val.start_minutes)),
                                end: new Date(column_date.setHours(val.end_hours, val.end_minutes)),
                                allDay: false,
                                resources: val.resources
                            })
                        }
                    });
                    break;
            }
            
        } // for loop
        // return events generated
        callback( events );
    }

);
});
</script>