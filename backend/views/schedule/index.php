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
                    'items' => ArrayHelper::map($availableTeachersDetails, 'id', 'name'),
                    'value' => null,
                    'placeholder' => 'Select Teacher',
                ],
            ]);
            ?>
        </div>
        <div id="next-prev-week-button" class="week-button">
            <button id="previous-week" class="btn btn-default btn-sm">Previous Week</button>
            <button id="next-week" class="btn btn-default btn-sm">Next Week</button>
        </div>
    </div>
    <div id='calendar' class="p-10"></div>
</div>
<script type="text/javascript">
var date = new Date();
var resources = [];
var day = moment(date).day();
var availableTeachersDetails = <?php echo Json::encode($availableTeachersDetails); ?>;
var uniqueAvailableTeachersDetails = removeDuplicates(availableTeachersDetails, "id");
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
    viewRender: function( view, element ) {
		if(view.name != 'resourceDay') {
			$('#next-prev-week-button').hide();
		} else {
			$('#next-prev-week-button').show();
		}
	},
    eventClick: function(event) {
        $(location).attr('href', event.url);
    },
    dayClick: function(date, allDay, jsEvent, view) {
        if (allDay) {
            var resources = getResources(date);
            refreshCalendar(resources, date);
        }
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

function removeDuplicates(value, key) {
    var unique = [];
    var lookup  = {};

    for (var i in value) {
        lookup[value[i][key]] = value[i];
    }

    for (i in lookup) {
        unique.push(lookup[i]);
    }

    return unique;
}

function loadTeachers(program) {
    var teachers = [];
    if((program == 'undefined') || (program == null)) {
        $.each( uniqueAvailableTeachersDetails, function( key, value ) {
            value.text = value.name;
            teachers.push(value);
        });
    }else {
        $.each( uniqueAvailableTeachersDetails, function( key, value ) {
            if ($.inArray(parseInt(program), value.programs) != -1) {
                value.text= value.name;
                teachers.push(value);
            }
        });
    }
    setTeachers(teachers);
}

function setTeachers(teachers){
    $('#teacher-selector').selectivity('destroy');
    $('#teacher-selector').selectivity({
        allowClear: true,
        items: teachers,
        value: null,
        placeholder: 'Select Teacher',
    });
 }

$(document).ready(function () {
    setTimeout(function(){
	$('#program-selector').on('change', function(e){
        var date = $('#calendar').fullCalendar('getDate');
		var resources = getResources(date);
        refreshCalendar(resources, date);
        loadTeachers(e.value);
	}); }, 3000);

    setTimeout(function(){
	$('#teacher-selector').on('change', function(e){
        var date = $('#calendar').fullCalendar('getDate');
		var resources = getResources(date);
        refreshCalendar(resources, date);
	}); }, 3000);
    $("#next-week").click(function() {
        var calendarDate = new Date($('#calendar').fullCalendar('getDate'));
        var date = moment(calendarDate).add('d', 7);
        var resources = getResources(date);
        refreshCalendar(resources, date);
      });
    $("#previous-week").click(function() {
        var calendarDate = new Date($('#calendar').fullCalendar('getDate'));
        var date = moment(calendarDate).subtract('d', 7);
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });
});
function getResources(date) {
    var day = moment(date).day();
    var resources = [];
    var selectedProgram = $('#program-selector').selectivity('value');
    var programSelected = (selectedProgram != 'undefined') && (selectedProgram != null);
    var selectedTeacher = $('#teacher-selector').selectivity('value');
    var teacherSelected = (selectedTeacher != 'undefined') && (selectedTeacher != null);
    if(!teacherSelected && !programSelected) {
        $.each( availableTeachersDetails, function( key, value ) {
            if (value.day == day) {
               resources.push(value);
            }
        });
    }
    if(!teacherSelected && programSelected){
        $.each( availableTeachersDetails, function( key, value ) {
            if (value.day == day && $.inArray(parseInt(selectedProgram), value.programs) != -1) {
               resources.push(value);
            }
        });
        loadTeachers(selectedProgram);
    }else if(teacherSelected){
        $.each( availableTeachersDetails, function( key, value ) {
            if (value.day == day && selectedTeacher == value.id) {
               resources.push(value);
            }
        });
    }
    return resources;
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
        viewRender: function( view, element ) {
            if(view.name != 'resourceDay') {
                $('#next-prev-week-button').hide();
            } else {
                $('#next-prev-week-button').show();
            }
        },
        dayClick: function(date, allDay, jsEvent, view) {
            if (allDay) {
                var resources = getResources(date);
                refreshCalendar(resources, date);
            }
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