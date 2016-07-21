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
<div id="myflashwrapper" style="display: none;" class="alert-success alert fade in"></div>
<div class="e1Div">
    <?= Html::checkbox('active', false, ['label' => 'Show All Teachers', 'id' => 'active' ]); ?>
</div>
<div id='calendar' class="p-10"></div>
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
    eventDrop: function(event, delta, revertFunc, jsEvent, ui, view) {
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
    },
    dayClick: function(date, allDay, jsEvent, view) {

        if (allDay) {
            // Clicked on the entire day
            $('#calendar')
                .fullCalendar('changeView', 'agendaDay')
                .fullCalendar('gotoDate',
                    date.getFullYear(), date.getMonth(), date.getDate());
        }
    }
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
            eventDrop: function(event, delta, revertFunc, jsEvent, ui, view) {
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
            },
             dayClick: function(date, allDay, jsEvent, view) {

                if (allDay) {
                    // Clicked on the entire day
                    $('#calendar')
                        .fullCalendar('changeView', 'agendaDay')
                        .fullCalendar('gotoDate',
                            date.getFullYear(), date.getMonth(), date.getDate());
                }
            }
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