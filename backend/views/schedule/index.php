<?php

use yii\helpers\Json;
use wbraganca\selectivity\SelectivityWidget;
use yii\helpers\ArrayHelper;
use common\models\Program;

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
    <div class="row">
        <div class="col-md-4">
            <?=
            SelectivityWidget::widget([
                'name' => 'Program',
                'id' => 'program-selector',
                'pluginOptions' => [
                    'allowClear' => true,
                    'items' => ArrayHelper::map(Program::find()->active()->all(), 'id', 'name'),
                    'value' => null,
                    'placeholder' => 'Select Program',
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
            <?=
            SelectivityWidget::widget([
                'name' => 'Teacher',
                'id' => 'teacher-selector',
                'pluginOptions' => [
                    'allowClear' => true,
                    'items' => ArrayHelper::map($teachersAvailabilities, 'id', 'name'),
                    'value' => null,
                    'placeholder' => 'Select Teacher',
                ],
            ]);
            ?>
        </div>
    </div>
    <div id='calendar' class="p-10"></div>
</div>
<script type="text/javascript">
var date = new Date();
var resources = [];
var day = moment(date).day();
var availableTeachersDetails = <?php echo Json::encode($availableTeachersDetails); ?>;
var events = <?php echo Json::encode($events); ?>;
$(document).ready(function() {
    $.each( availableTeachersDetails, function( key, value ) {
        if (value.day == day) {
           resources.push(value);
        }
    });
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
    droppable: true,
    resources:  resources,
    events: events,
    eventClick: function(event) {
        $(location).attr('href', event.url);
    },
    dayClick: function(date, allDay, jsEvent, view) {
        if (allDay) {
            var resources = getResources(date);
            refreshCalendar(resources, date);
        }
    },
    eventAfterAllRender: function (view, element) {
        eventAfterAllRender();
    },
    });
    $(".fc-button-prev, .fc-button-next").click(function(){
        $(".fc-view-month .fc-event").hide();
        var date = $('#calendar').fullCalendar('getDate');
        var view = $('#calendar').fullCalendar('getView');
        if(view.name == 'resourceDay'){
            var resources = getResources(date);
            refreshCalendar(resources, date);
        }
    });
    $(".fc-button-month, .fc-button-today").click(function(){
        $(".fc-view-month .fc-event").hide();
    });

});

$(document).ready(function () {
    setTimeout(function(){
	$('#program-selector').on('selectivity-selected', function(e){
        var date = $('#calendar').fullCalendar('getDate');
		var day = moment(date).day();
        var resources = [];
        $.each( availableTeachersDetails, function( key, value ) {
            if (value.day === day && $.inArray(e.id, value.programs) !== -1) {
               resources.push(value);
            }
        });
        refreshCalendar(resources, date);
	}); }, 3000);

    setTimeout(function(){
	$('#teacher-selector').on('selectivity-selected', function(e){
        var date = $('#calendar').fullCalendar('getDate');
		var day = moment(date).day();
        var resources = [];
        $.each( availableTeachersDetails, function( key, value ) {
            if (value.day === day && e.id === value.id) {
               resources.push(value);
            }
        });
        refreshCalendar(resources, date);
	}); }, 3000);
});
function getResources(date) {
    var day = moment(date).day();
    var resources = [];
    var selectedProgram = $('#program-selector').selectivity('value');
    var programSelected = (selectedProgram !== 'undefined') && (selectedProgram !== null);
    var selectedTeacher = $('#teacher-selector').selectivity('value');
    var teacherSelected = (selectedTeacher !== 'undefined') && (selectedTeacher !== null);
    if(!teacherSelected && !programSelected) {
        $.each( availableTeachersDetails, function( key, value ) {
            if (value.day == day) {
               resources.push(value);
            }
        });
    }
    if(!teacherSelected && programSelected){
        $.each( availableTeachersDetails, function( key, value ) {
            if (value.day === day && $.inArray(selectedProgram, value.programs) !== -1) {
               resources.push(value);
            }
        });
    }
    if(teacherSelected){
        var resources = [];
        $.each( availableTeachersDetails, function( key, value ) {
            if (value.day === day && selectedTeacher === value.id) {
               resources.push(value);
            }
        });
    }
    return resources;
}

function eventAfterAllRender () {
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
}
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
        droppable: true,
        resources:  resources,
        events: events,
        dayClick: function(date, allDay, jsEvent, view) {
            if (allDay) {
                var resources = getResources(date);
                refreshCalendar(resources, date);
            }
        },
        eventAfterAllRender: function (view, element) {
            eventAfterAllRender();
        },
    });
    $(".fc-button-prev, .fc-button-next").click(function(){
        $(".fc-view-month .fc-event").hide();
        var date = $('#calendar').fullCalendar('getDate');
        var view = $('#calendar').fullCalendar('getView');
        if(view.name == 'resourceDay'){
            var resources = getResources(date);
            refreshCalendar(resources, date);
        }
    })
    $(".fc-button-month, .fc-button-today").click(function(){
        $(".fc-view-month .fc-event").hide();
    })
}
</script>