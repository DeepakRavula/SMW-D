<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Schedule';
$this->params['breadcrumbs'][] = $this->title;
?>
<link type="text/css" href="/plugins/fullcalendar/fullcalendar.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar/fullcalendar.js"></script>
<style>
  .e1Div{
    right: 0 !important;
  }
</style>
<div id="myflashwrapper" style="display: none;" class="alert-success alert fade in"></div>
<div id="myflashinfo" style="display: none;" class="alert-info alert fade in"></div>
<div class="schedule-index">
<div id="next-prev-week-button" class="week-button">
<button id="previous-week" class="btn btn-default btn-sm">Previous Week</button>
<button id="next-week" class="btn btn-default btn-sm">Next Week</button>
</div>
<div class="e1Div">
    <?= Html::checkbox('active', false, ['label' => 'Show All Teachers', 'id' => 'active']); ?>
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
    editable: false,
    droppable: false,
    resources:  <?php echo Json::encode($teachersWithClass); ?>,
    events: <?php echo Json::encode($events); ?>,
    eventClick: function(event) {
        $(location).attr('href', event.url);
    },
	viewRender: function( view, element ) {
		if(view.name !== 'resourceDay') {
			$('#next-prev-week-button').hide();
		} else {
			$('#next-prev-week-button').show();
		}
	},
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
                url: "<?php echo Url::to(['schedule/update-events']); ?>",
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
    $(".fc-button-month, .fc-button-prev, .fc-button-next, .fc-button-today").click(function(){
        $(".fc-view-month .fc-event").hide();
    })
});

$(document).ready(function () {
$("#next-week").click(function() {
    var resources = <?php echo Json::encode($teachersWithClass); ?>;
	var calendarDate = new Date($('#calendar').fullCalendar('getDate'));
    calendarDate.setDate(calendarDate.getDate() + 7);
	var nextWeek = calendarDate.getDate()+'-'+ (calendarDate.getMonth()+1) +'-'+calendarDate.getFullYear();
	var date = moment(nextWeek,'D-M-YYYY', true).format();
    refreshCalendar(resources, date);
  });
  $("#previous-week").click(function() {
    var resources = <?php echo Json::encode($teachersWithClass); ?>;
	var calendarDate = new Date($('#calendar').fullCalendar('getDate'));
    calendarDate.setDate(calendarDate.getDate() - 7);
	var previousWeek = calendarDate.getDate()+'-'+ (calendarDate.getMonth()+1) +'-'+calendarDate.getFullYear();
	var date = moment(previousWeek,'D-M-YYYY', true).format();
    refreshCalendar(resources, date);
  });
$("#active").change(function() {
    var resources = <?php echo Json::encode($teachersWithClass); ?>;
    if( $(this).is(':checked') ){
        var AllResources = <?php echo Json::encode($allTeachers); ?>;
        var date = $('#calendar').fullCalendar('getDate');
        var day = moment(date).day();
        var resources = [];
        $.each( AllResources, function( key, value ) {
            if (value.day == day) {
               resources.push(value);
            }
        });
        refreshCalendar(resources, date);
    }
    refreshCalendar(resources, date);
  });
});
function refreshCalendar(resources, date) {
    $('#calendar').html('');
    $('#calendar').unbind().removeData().fullCalendar({
        defaultDate: date,
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
    	droppable: false,
        resources:  resources,
        events: <?php echo Json::encode($events); ?>,
        dayClick: function(date, allDay, jsEvent, view) {
            if (allDay) {
                // Clicked on the entire day
                $('#calendar').fullCalendar('changeView', 'resourceDay');
                $('#calendar').fullCalendar('gotoDate', date);
                $('#calendar').fullCalendar({
                    resources:  <?php echo Json::encode($teachersWithClass); ?>,
                    events: <?php echo Json::encode($events); ?>,
                });
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
    $(".fc-button-prev, .fc-button-next").click(function(){
        $(".fc-view-month .fc-event").hide();
        if( $('#active').is(':checked') ){
            var AllResources = <?php echo Json::encode($allTeachers); ?>;
            var date = $('#calendar').fullCalendar('getDate');
            var day = moment(date).day();
            var resources = [];//
            $.each( AllResources, function( key, value ) {
                if (value.day == day) {
                   resources.push(value);
                }
            });
            refreshCalendar(resources, date);
        }
    })
    $(".fc-button-month, .fc-button-today").click(function(){
        $(".fc-view-month .fc-event").hide();
    })
  }
</script>
