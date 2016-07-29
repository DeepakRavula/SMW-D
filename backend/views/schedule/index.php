<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Schedule';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="myflashwrapper" style="display: none;" class="alert-success alert fade in"></div>
<div id="myflashinfo" style="display: none;" class="alert-info alert fade in"></div>
<div class="schedule-index">
<div class="e1Div">
    <?= Html::checkbox('active', false, ['label' => 'Show All Teachers', 'id' => 'active' ]); ?>
</div>
<div id='calendar' class="p-10"></div>
</div>
<script type="text/javascript">
$(document).ready(function() { 
    var oldEventResource= "";
    var oldEvent = "";
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
	titleFormat: 'DD-MMM-YYYY, dddd',
    defaultView: 'resourceDay',
    minTime: "<?php echo $from_time; ?>",
    maxTime: "<?php echo $to_time; ?>",
    slotDuration: "00:15:01",
    editable: true,
    droppable: true,
    resources:  <?php echo Json::encode($teachersWithClass); ?>,
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
    eventDragStart: function(event, jsEvent, ui, view) {
        oldEvent = event;
        oldEventResource = event.resources;
        console.log(oldEvent);
	},
    eventDrop: function(event, delta, revertFunc, jsEvent, ui, view) {
        var newEventResource = event.resources;
        if(Number(newEventResource) != Number(oldEventResource))
        {
            revertFunc();
            /*$('#calendar').fullCalendar('removeEvents');
            $('#calendar').fullCalendar('addEventSource', <?php echo Json::encode($events); ?>);         
            $('#calendar').fullCalendar('rerenderEvents' );*/
            
           // $('#calendar').fullCalendar( 'removeEvents', event.id);
            //$('#calendar').fullCalendar('addEventSource', oldEvent);  
            //$('#calendar').fullCalendar( 'renderEvent', oldEvent , 'stick');
            
        } else {
            $.ajax({
                url: "<?php echo Url::to(['schedule/update-events']);?>",
                type: "POST",
                contentType: 'application/json',
                dataType: "json",
                data: JSON.stringify({
                    "id": event.id,
                    "minutes": delta.asMinutes(),
                }),
                success: function(response) {
                },
                error: function() {
                }
            });
            
            $('#myflashwrapper').html("Re-scheduled successfully").fadeIn().delay(3000).fadeOut();
        }
    },
    dayClick: function(date, allDay, jsEvent, view) {
        if (allDay) {
            // Clicked on the entire day
            $('#calendar').fullCalendar('changeView', 'resourceDay');
            $('#calendar').fullCalendar('gotoDate', date);
            $('#calendar').fullCalendar(
                {
                    resources:  <?php echo Json::encode($teachersWithClass); ?>,
                    events: <?php echo Json::encode($events); ?>,
                }
            );
        }
    },
    eventAfterAllRender: function (view, element) {
        var date = new Date($('#calendar').fullCalendar('getDate'));
        var count = 0;
        $('#calendar').fullCalendar('clientEvents', function(event) {
            var startTime = new Date(event.start);
            var eventDate = startTime.getDate() + "/" + startTime.getMonth() + "/" + startTime.getFullYear();
            var currentDate = date.getDate() + "/" + date.getMonth() + "/" + date.getFullYear();
            if(eventDate == currentDate) {
               count++;
            }
        });
        
        if(count==0){
            $('#myflashinfo').html("No lessons scheduled for the day").fadeIn().delay(1000).fadeOut();
        }
    },
  });
  
  $("#active").change(function() {
       var resources = <?php echo Json::encode($teachersWithClass); ?>;
        if( $(this).is(':checked') ){
            var resources = <?php echo Json::encode($allTeachers); ?>;
        }
        $('#calendar').html('');
        $('#calendar').unbind().removeData().fullCalendar({
            header: {
              left: 'prev,next today',
              center: 'title',
              right: 'month,agendaWeek,resourceDay'
            },    
            titleFormat: 'DD-MMM-YYYY, dddd',
            defaultView: 'resourceDay',
            minTime: "<?php echo $from_time; ?>",
            maxTime: "<?php echo $to_time; ?>",
            slotDuration: "00:15:01",
            editable: true,
            droppable: true,
            resources:  resources,
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
            eventDragStart: function(event, jsEvent, ui, view) {
                oldEventResource = event.resources;
                oldEvent = event;
            },
            eventDrop: function(event, delta, revertFunc, jsEvent, ui, view) {
                var newEventResource = event.resources;
                if(Number(newEventResource) != Number(oldEventResource))
                {
                    revertFunc();
                } else {
                    $.ajax({
                        url: "<?php echo Url::to(['schedule/update-events']);?>",
                        type: "POST",
                        contentType: 'application/json',
                        dataType: "json",
                        data: JSON.stringify({
                            "id": event.id,
                            "minutes": delta.asMinutes(),
                        }),
                        success: function(response) {
                        },
                        error: function() {
                        }
                    });
                    
                    $('#myflashwrapper').html("Re-scheduled successfully").fadeIn().delay(3000).fadeOut();
                }
            },
            dayClick: function(date, allDay, jsEvent, view) {
                if (allDay) {
                    // Clicked on the entire day
                    $('#calendar').fullCalendar('changeView', 'resourceDay');
                    $('#calendar').fullCalendar('gotoDate', date);
                    $('#calendar').fullCalendar({resources:  <?php echo Json::encode($teachersWithClass); ?>,
    events: <?php echo Json::encode($events); ?>,});
                }
            },
            eventAfterAllRender: function (view, element) {
                var count = 0;
                var date = new Date($('#calendar').fullCalendar('getDate'));
                $('#calendar').fullCalendar('clientEvents', function(event) {
                    var startTime = new Date(event.start);
                    var eventDate = startTime.getDate() + "/" + startTime.getMonth() + "/" + startTime.getFullYear();
                    var currentDate = date.getDate() + "/" + date.getMonth() + "/" + date.getFullYear();
                    if(eventDate == currentDate) {
                       count++;
                    }
                    
                });
                if(count==0){
                    $('#myflashinfo').html("No lessons scheduled for the day").fadeIn().delay(3000).fadeOut();
                }
            },
        });
        $(".fc-button-month, .fc-button-prev, .fc-button-next, .fc-button-today").click(function(){
            $(".fc-view-month .fc-event").hide();      
        })
  });
    $(".fc-button-month, .fc-button-prev, .fc-button-next, .fc-button-today").click(function(){
        $(".fc-view-month .fc-event").hide();      
    })
});
</script>