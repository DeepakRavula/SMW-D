<?php

use yii\helpers\Json;
use wbraganca\selectivity\SelectivityWidget;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\CalendarEventColor;

/* @var $this yii\web\View */

$this->title = 'Schedule';
$this->params['breadcrumbs'][] = $this->title;
?>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<style>
  .e1Div{
    right: 0 !important;
  }
</style>
<?php
    $storeClosed = CalendarEventColor::findOne(['cssClass' => 'store-closed']);
    $teacherAvailability = CalendarEventColor::findOne(['cssClass' => 'teacher-availability']);
    $teacherUnavailability = CalendarEventColor::findOne(['cssClass' => 'teacher-unavailability']);
    $privateLesson = CalendarEventColor::findOne(['cssClass' => 'private-lesson']);
    $groupLesson = CalendarEventColor::findOne(['cssClass' => 'group-lesson']);
    $firstLesson = CalendarEventColor::findOne(['cssClass' => 'first-lesson']);
    $teacherSubstitutedLesson = CalendarEventColor::findOne(['cssClass' => 'teacher-substituted']);
    $rescheduledLesson = CalendarEventColor::findOne(['cssClass' => 'lesson-rescheduled']);
    $missedLesson = CalendarEventColor::findOne(['cssClass' => 'lesson-missed']);
    $this->registerCss(
        ".fc-bgevent { background-color: " . $teacherAvailability->code . " !important; }
        .holiday, .fc-event .holiday .fc-event-time, .holiday a { background-color: " . $storeClosed->code . " !important;
            border: 1px solid " . $storeClosed->code . " !important; }
        .fc-bg { background-color: " . $teacherUnavailability->code . " !important; }
        .fc-today { background-color: " . $teacherUnavailability->code . " !important; }
        .private-lesson, .fc-event .private-lesson .fc-event-time, .private-lesson a {
            border: 1px solid " . $privateLesson->code . " !important;
            background-color: " . $privateLesson->code . " !important; }
        .first-lesson, .fc-event .first-lesson .fc-event-time, .first-lesson a {
            border: 1px solid " . $firstLesson->code . " !important;
            background-color: " . $firstLesson->code . " !important; }
        .group-lesson, .fc-event .group-lesson .fc-event-time, .group-lesson a {
            border: 1px solid " . $groupLesson->code . " !important;
            background-color: " . $groupLesson->code . " !important; }
        .teacher-substituted, .fc-event .teacher-substituted .fc-event-time, .teacher-substituted a {
            border: 1px solid " . $teacherSubstitutedLesson->code . " !important;
            background-color: " . $teacherSubstitutedLesson->code . " !important; }
        .lesson-rescheduled, .fc-event .lesson-rescheduled .fc-event-time, .lesson-rescheduled a {
            border: 1px solid " . $rescheduledLesson->code . " !important;
            background-color: " . $rescheduledLesson->code . " !important; }
        .lesson-missed, .fc-event .lesson-missed .fc-event-time, .lesson-missed a {
            border: 1px solid " . $missedLesson->code . " !important;
            background-color: " . $missedLesson->code . " !important; }"
    );
?>
<div class="schedule-index">
    <div class="row schedule-filter">
        <div class="col-md-2 m-t-10 text-center"><p>Go to Date</p></div>
        <div class="col-md-2 p-0">
            <div class="input-group date">
                <input type="text" class="form-control" value=<?=(new \DateTime())->format('d-m-Y')?>>
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </div>
            </div>
        </div>
        <div class="filter">
            <div class="col-md-1 m-t-10 text-right"><p>Filter by</p></div>
            <div class="col-md-3 p-0">
                <?=
                SelectivityWidget::widget([
                    'name' => 'Program',
                    'id' => 'program-selector',
                    'pluginOptions' => [
                        'allowClear' => true,
                        'items' => ArrayHelper::map(Program::find()->active()->all(), 'id', 'name'),
                        'value' => null,
                        'placeholder' => 'Program',
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-3">
                <?=
                SelectivityWidget::widget([
                    'name' => 'Teacher',
                    'id' => 'teacher-selector',
                    'pluginOptions' => [
                        'allowClear' => true,
                        'items' => ArrayHelper::map($availableTeachersDetails, 'id', 'name'),
                        'value' => null,
                        'placeholder' => 'Teacher',
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
    <div id='calendar'></div>
</div>
<script type="text/javascript">
var date = new Date();
var currentDate = moment(date).format('YYYY-MM-DD 00:00:00');
var isHoliday = false;
var resources = [];
var day = moment(date).day();
var teachersAvailabilitiesAllDetails = <?php echo Json::encode($teachersAvailabilitiesAllDetails); ?>;
var teachersAvailabilitiesDetails = <?php echo Json::encode($teachersAvailabilitiesDetails); ?>;
var availableTeachersDetails = <?php echo Json::encode($availableTeachersDetails); ?>;
var uniqueAvailableTeachersDetails = removeDuplicates(availableTeachersDetails, "id");
var events = <?php echo Json::encode($events); ?>;
var holidays = <?php echo Json::encode($holidays); ?>;
isclassroom = false;
$(document).ready(function() {
	$('.input-group.date').datepicker ({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true
    });
    $.each( holidays, function( key, value ) {
        if (value.date == currentDate) {
            isHoliday = true;
            resources.push({
                id: '0',
                title: 'Holiday'
            });
        }
    });
    if(day == 0 && !isHoliday) {
        resources.push({
            id: '0',
            title: 'Sunday-Holiday'
        });
    } else if(!isHoliday) {
        $.each( availableTeachersDetails, function( key, value ) {
            if (value.day == day) {
                resources.push({
                    id: value.id,
                    title: value.name
                });
            }
        });
    }

    $('#calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        customButtons: {
            teacherView: {
                text: 'Teacher View',
                click: function() {
                    var resources = getResources(date);
                    refreshCalendar(resources, date);
                }
            },
            classroomView: {
                text: 'Classroom View',
                click: function() {
                    showclassroomCalendar(date);
                }
            }
        },
        header: {
            left: 'today',
            center: 'prevYear prev title next nextYear',
            right: 'teacherView classroomView'
        },
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: "<?php echo $from_time; ?>",
        maxTime: "<?php echo $to_time; ?>",
        slotDuration: "00:15:00",
        editable: false,
        droppable: false,
        resources: resources,
        events: events,
        eventClick: function(event) {
            $(location).attr('href', event.url);
        },
        viewRender: function() {
            $('.fc-teacherView-button').addClass('fc-state-active');
        }
    });

    $(".fc-prev-button, .fc-next-button").click(function(){
        var date = $('#calendar').fullCalendar('getDate');
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });

    $(".fc-today-button").click(function(){
        var date = moment(new Date());
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });

    $(".fc-prevYear-button").click(function() {
        var calendarDate = new Date($('#calendar').fullCalendar('getDate'));
        var date = moment(calendarDate).add(1, 'year').subtract(1, 'weeks');
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });

    $(".fc-nextYear-button").click(function() {
        var calendarDate = new Date($('#calendar').fullCalendar('getDate'));
        var date = moment(calendarDate).subtract(1, 'year').add(1, 'weeks');
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });

    if (!isHoliday) {
        addAllAvailabilityEvents();
    }
});

function addAllAvailabilityEvents() {
    $('#calendar').fullCalendar( 'addEventSource',
    function(start, end, status, callback) {
        var currentDay = moment(start).day();
        var currentDate = moment(start).format('YYYY-MM-DD');
        var start = moment(start).format('YYYY-MM-DD 00:00:00');
        var end = moment(start).format('YYYY-MM-DD 23:59:59');

        var events = [];
        if(currentDay === 0) {
            events.push({
                title: '',
                start: start,
                end: end,
                resourceId: 0,
                allDay: false,
                className: 'holiday',
                rendering: 'background'
            });
        } else {
            $.each( teachersAvailabilitiesAllDetails, function( key, value ) {
                if(value.day == currentDay) {
                    var startTime = moment(currentDate+' '+value.from_time).format('YYYY-MM-DD HH:mm:ss');
                    var endTime = moment(currentDate+' '+value.to_time).format('YYYY-MM-DD HH:mm:ss');
                    events.push({
                        title: '',
                        start: startTime,
                        end: endTime,
                        resourceId: value.id,
                        allDay: false,
                        rendering: 'background'
                    });
                }
            });
        }
        callback( events );
    });
}

function addAvailabilityEvents() {
    var holidays = <?php echo Json::encode($holidays); ?>;
    $('#calendar').fullCalendar( 'addEventSource',
    function(start, end, status, callback) {
        var selectedProgram = $('#program-selector').selectivity('value');
        var programSelected = (selectedProgram != 'undefined') && (selectedProgram != null);
        var selectedTeacher = $('#teacher-selector').selectivity('value');
        var teacherSelected = (selectedTeacher != 'undefined') && (selectedTeacher != null);
        var day = moment(start).day();
        var currentDate = moment(start).format('YYYY-MM-DD');
        var start = moment(start).format('YYYY-MM-DD 00:00:00');
        var end = moment(start).format('YYYY-MM-DD 23:59:59');
        var events = [];
        var isHoliday = false;
        $.each( holidays, function( key, value ) {
            if (value.date == start) {
                isHoliday = true;
            }
        });
        if(day === 0 && !isHoliday) {
            events.push({
                title: '',
                start: start,
                end: end,
                resourceId: 0,
                allDay: false,
                className: 'holiday',
                rendering: 'background'
            });
        } else if(!isHoliday) {
            if(!teacherSelected && !programSelected) {
                $.each( teachersAvailabilitiesAllDetails, function( key, value ) {
                    if (value.day == day) {
                        var startTime = moment(currentDate+' '+value.from_time).format('YYYY-MM-DD HH:mm:ss');
                        var endTime = moment(currentDate+' '+value.to_time).format('YYYY-MM-DD HH:mm:ss');
                        events.push({
                            title: '',
                            start: startTime,
                            end: endTime,
                            resourceId: value.id,
                            allDay: false,
                            rendering: 'background'
                        });
                    }
                });
            }
            if(!teacherSelected && programSelected){
                $.each( teachersAvailabilitiesAllDetails, function( key, value ) {
                    if (value.day == day && $.inArray(parseInt(selectedProgram), value.programs) != -1) {
                        var startTime = moment(currentDate+' '+value.from_time).format('YYYY-MM-DD HH:mm:ss');
                        var endTime = moment(currentDate+' '+value.to_time).format('YYYY-MM-DD HH:mm:ss');
                        events.push({
                            title: '',
                            start: startTime,
                            end: endTime,
                            resourceId: value.id,
                            allDay: false,
                            rendering: 'background'
                        });
                    }
                });
            }else if(teacherSelected){
                $.each( teachersAvailabilitiesAllDetails, function( key, value ) {
                    if (value.day == day && selectedTeacher == value.id) {
                        var startTime = moment(currentDate+' '+value.from_time).format('YYYY-MM-DD HH:mm:ss');
                        var endTime = moment(currentDate+' '+value.to_time).format('YYYY-MM-DD HH:mm:ss');
                        events.push({
                            title: '',
                            start: startTime,
                            end: endTime,
                            resourceId: value.id,
                            allDay: false,
                            rendering: 'background'
                        });
                    }
                });
            }
        }
        callback( events );
    });
}

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
	$('#teacher-selector').on('change', function(){
        var date = $('#calendar').fullCalendar('getDate');
		var resources = getResources(date);
        refreshCalendar(resources, date);
	}); }, 3000);

    $('#datePicker-date').on('change', function(){
        var date = moment($(this).val(), 'DD-MM-YYYY', true).format('YYYY-MM-DD');
		if (!isclassroom) {
            var resources = getResources(date);
            refreshCalendar(resources, date);
        } else {
            showclassroomCalendar(date);
        }
	});
});

function showclassroomCalendar(date) {
    isclassroom = true;
    $('#calendar').html('');
    $('#calendar').unbind().removeData().fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        defaultDate: date,
        customButtons: {
            teacherView: {
                text: 'Teacher View',
                click: function() {
                    var date = $('#calendar').fullCalendar('getDate');
                    var resources = getResources(date);
                    refreshCalendar(resources, date);
                }
            },
            classroomView: {
                text: 'Classroom View',
                click: function() {
                    showclassroomCalendar(date);
                }
            }
        },
        header: {
            left: 'today',
            center: 'prevYear prev title next nextYear',
            right: 'teacherView classroomView'
        },
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: "<?php echo $from_time; ?>",
        maxTime: "<?php echo $to_time; ?>",
        slotDuration: "00:15:00",
        editable: false,
        droppable: false,
        resources: <?php echo Json::encode($classroomResource); ?>,
        events: <?php echo Json::encode($classroomEvents); ?>,
        viewRender: function() {
            $('.fc-classroomView-button').addClass('fc-state-active');
            $('.filter').hide();
        }
    });

    $(".fc-nextYear-button").click(function(e) {
        var calendarDate = new Date($('#calendar').fullCalendar('getDate'));
        var date = moment(calendarDate).subtract(1, 'year').add(1, 'weeks');
        showclassroomCalendar(date);
    });

    $(".fc-prevYear-button").click(function(e) {
        var calendarDate = new Date($('#calendar').fullCalendar('getDate'));
        var date = moment(calendarDate).add(1, 'year').subtract(1, 'weeks');
        showclassroomCalendar(date);
    });
}

function getResources(date) {
    var day = moment(date).day();
    var currentDate = moment(date).format('YYYY-MM-DD 00:00:00');
    var resources = [];
    var selectedProgram = $('#program-selector').selectivity('value');
    var programSelected = (selectedProgram != 'undefined') && (selectedProgram != null);
    var selectedTeacher = $('#teacher-selector').selectivity('value');
    var teacherSelected = (selectedTeacher != 'undefined') && (selectedTeacher != null);
    $.each( holidays, function( key, value ) {
        if (value.date == currentDate) {
            resources.push({
                id: '0',
                title: 'Holiday'
            });
        }
    });
    if(day == 0 && $.isEmptyObject(resources)) {
        resources.push({
            id: '0',
            title: 'Sunday-Holiday'
        });
    } else if($.isEmptyObject(resources)) {
        if(!teacherSelected && !programSelected) {
            $.each( availableTeachersDetails, function( key, value ) {
                if (value.day == day) {
                    resources.push({
                        id: value.id,
                        title: value.name
                    });
                }
            });
        }
        if(!teacherSelected && programSelected){
            $.each( availableTeachersDetails, function( key, value ) {
                if (value.day == day && $.inArray(parseInt(selectedProgram), value.programs) != -1) {
                    resources.push({
                        id: value.id,
                        title: value.name
                    });
                }
            });
            if($.isEmptyObject(resources)) {
                resources.push({
                    id: '',
                    title: 'No Teacher Available for the selected Program'
                });
            }
            loadTeachers(selectedProgram);
        }else if(teacherSelected){
            $.each( availableTeachersDetails, function( key, value ) {
                if (value.day == day && selectedTeacher == value.id) {
                    resources.push({
                        id: value.id,
                        title: value.name
                    });
                }
            });
            if($.isEmptyObject(resources)) {
                resources.push({
                    id: '',
                    title: 'Selected Teacher Not Available'
                });
            }
        }
    }
    return resources;
}

function refreshCalendar(resources, date) {
    isclassroom = false;
    $('#calendar').html('');
    $('#calendar').unbind().removeData().fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        defaultDate: date,
        customButtons: {
            teacherView: {
                text: 'Teacher View',
                click: function() {
                    var date = $('#calendar').fullCalendar('getDate');
                    var resources = getResources(date);
                    refreshCalendar(resources, date);
                }
            },
            classroomView: {
                text: 'Classroom View',
                click: function() {
                    var date = $('#calendar').fullCalendar('getDate');
                    showclassroomCalendar(date);
                }
            }
        },
        header: {
            left: 'today',
            center: 'prevYear prev title next nextYear',
            right: 'teacherView classroomView'
        },
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: "<?php echo $from_time; ?>",
        maxTime: "<?php echo $to_time; ?>",
        slotDuration: "00:15:00",
        editable: false,
        droppable: false,
        resources:  resources,
        events: events,
        viewRender: function() {
            $('.fc-teacherView-button').addClass('fc-state-active');
            $('.filter').show();
        }
    });

    $(".fc-prev-button, .fc-next-button").click(function(){
        $(".fc-event").hide();
        var date = $('#calendar').fullCalendar('getDate');
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });

    $(".fc-today-button").click(function(){
        var date = moment(new Date());
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });

    $(".fc-prevYear-button").click(function() {
        var calendarDate = new Date($('#calendar').fullCalendar('getDate'));
        var date = moment(calendarDate).add(1, 'year').subtract(1, 'weeks');
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });

    $(".fc-nextYear-button").click(function() {
        var calendarDate = new Date($('#calendar').fullCalendar('getDate'));
        var date = moment(calendarDate).subtract(1, 'year').add(1, 'weeks');
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });

    addAvailabilityEvents();
}
</script>
