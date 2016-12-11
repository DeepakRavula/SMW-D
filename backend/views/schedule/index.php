<?php

use yii\helpers\Json;
use wbraganca\selectivity\SelectivityWidget;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\CalendarEventColor;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Schedule';
$this->params['breadcrumbs'][] = $this->title;
?>
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
        .teacher-substituted, .fc-event .lesson-assigned-teacher .fc-event-time, .lesson-assigned-teacher a {
            border: 1px solid " . $teacherSubstitutedLesson->code . " !important;
            background-color: " . $teacherSubstitutedLesson->code . " !important; }
        .lesson-rescheduled, .fc-event .lesson-reschedule-date .fc-event-time, .lesson-reschedule-date a {
            border: 1px solid " . $rescheduledLesson->code . " !important;
            background-color: " . $rescheduledLesson->code . " !important; }"
    );
?>
<div class="schedule-index">
    <div class="row schedule-filter">
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
   
        <div id="next-prev-week-button" class="col-md-3 week-button m-t-10 m-l-10">
            <button id="previous-week" class="btn btn-default btn-sm">Previous Week</button>
            <button id="next-week" class="btn btn-default btn-sm">Next Week</button>
    	</div>
		 </div>
		 <div class="e1Div">
    <?= Html::checkbox('active', false, ['label' => 'Show classroom', 'id' => 'active' ]); ?>
</div>
    <div id='calendar' class="p-10"></div>
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
$(document).ready(function() {
    $.each( holidays, function( key, value ) {
        if (value.date == currentDate) {
            isHoliday = true;
            resources.push({
                id: '0',
                title: 'Holiday'
            })
        }
    });
    if(day == 0 && !isHoliday) {
        resources.push({
            id: '0',
            title: 'Sunday-Holiday'
        })
    } else if(!isHoliday) {
        $.each( availableTeachersDetails, function( key, value ) {
            if (value.day == day) {
                resources.push({
                    id: value.id,
                    title: value.name
                })
            }
        });
    }
    $('#calendar').fullCalendar({
    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
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
    viewRender: function( view, element ) {
		if(view.name != 'agendaDay') {
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
    $(".fc-prev-button, .fc-next-button").click(function(){ 
        $(".fc-view-month .fc-event").hide();
        var date = $('#calendar').fullCalendar('getDate');
        var view = $('#calendar').fullCalendar('getView');
        if(view.name == 'agendaDay'){
            var resources = getResources(date);
            refreshCalendar(resources, date);
        }
    });
    $(".fc-month-button, .fc-today-button").click(function(){
        $(".fc-view-month .fc-event").hide();
    });
    $(".fc-agendaDay-button, .fc-today-button").click(function(){
        var date = $('#calendar').fullCalendar('getDate');
        if ($(this).className === 'fc-today-button') {
            var date = moment(new Date());
        }
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });
        addAllAvailabilityEvents();
	$("#active").change(function() {
	   $.each( holidays, function( key, value ) {
			if (value.date == currentDate) {
				isHoliday = true;
				resources.push({
					id: '0',
					title: 'Holiday'
				})
			}
		});
		if(day == 0 && !isHoliday) {
			resources.push({
				id: '0',
				title: 'Sunday-Holiday'
			})
		} else if(!isHoliday) {
			$.each( availableTeachersDetails, function( key, value ) {
				if (value.day == day) {
					resources.push({
						id: value.id,
						title: value.name
					})
				}
			});
		}
		$('#calendar').html('');
		$('#calendar').unbind().removeData().fullCalendar({
			schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
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
			viewRender: function( view, element ) {
				$('.schedule-filter').show();
			},
			eventClick: function(event) {
				$(location).attr('href', event.url);
			},
			dayClick: function(date, allDay, jsEvent, view) {
				if (allDay) {
					var resources = getResources(date);
					refreshCalendar(resources, date);
				}
			}
    	});
	    $(".fc-prev-button, .fc-next-button").click(function(){ 
        $(".fc-view-month .fc-event").hide();
        var date = $('#calendar').fullCalendar('getDate');
        var view = $('#calendar').fullCalendar('getView');
        if(view.name == 'agendaDay'){
            var resources = getResources(date);
            refreshCalendar(resources, date);
        }
    });
    $(".fc-month-button, .fc-today-button").click(function(){
        $(".fc-view-month .fc-event").hide();
    });
    $(".fc-agendaDay-button, .fc-today-button").click(function(){
        var date = $('#calendar').fullCalendar('getDate');
        if ($(this).className === 'fc-today-button') {
            var date = moment(new Date());
        }
        var resources = getResources(date);
        refreshCalendar(resources, date);
    });
        addAllAvailabilityEvents();
        if( $(this).is(':checked') ){
			$('#calendar').html('');
			$('#calendar').unbind().removeData().fullCalendar({
				schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
				header: {
				  left: 'prev,next today',
				  center: 'title',
				  right: 'month,agendaWeek,agendaDay'
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
				viewRender: function( view, element ) {
					$('.schedule-filter').hide();
				}
    		});
		}
  	});
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
            })
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
                    })
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
            })
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
                        })
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
                        })
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
                        })
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
            })
        }
    });
    if(day == 0 && $.isEmptyObject(resources)) {
        resources.push({
            id: '0',
            title: 'Sunday-Holiday'
        })
    } else if($.isEmptyObject(resources)) {
        if(!teacherSelected && !programSelected) {
            $.each( availableTeachersDetails, function( key, value ) {
                if (value.day == day) {
                    resources.push({
                        id: value.id,
                        title: value.name
                    })
                }
            });
        }
        if(!teacherSelected && programSelected){
            $.each( availableTeachersDetails, function( key, value ) {
                if (value.day == day && $.inArray(parseInt(selectedProgram), value.programs) != -1) {
                    resources.push({
                        id: value.id,
                        title: value.name
                    })
                }
            });
            if($.isEmptyObject(resources)) {
                resources.push({
                    id: '',
                    title: 'No Teacher Available for the selected Program'
                })
            }
            loadTeachers(selectedProgram);
        }else if(teacherSelected){
            $.each( availableTeachersDetails, function( key, value ) {
                if (value.day == day && selectedTeacher == value.id) {
                    resources.push({
                        id: value.id,
                        title: value.name
                    })
                }
            });
            if($.isEmptyObject(resources)) {
                resources.push({
                    id: '',
                    title: 'Selected Teacher Not Available'
                })
            }
        }
    }
    return resources;
}

function refreshCalendar(resources, date) {
    $('#calendar').html('');
    $('#calendar').unbind().removeData().fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        defaultDate: date,
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,agendaWeek,agendaDay'
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
        viewRender: function( view, element ) {
            if(view.name != 'agendaDay') {
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
    $(".fc-prev-button, .fc-next-button").click(function(){
        $(".fc-view-month .fc-event").hide();
        var date = $('#calendar').fullCalendar('getDate');
        var view = $('#calendar').fullCalendar('getView');
        if(view.name == 'agendaDay'){
            var resources = getResources(date);
            refreshCalendar(resources, date);
        }
    })
    $(".fc-month-button, .fc-today-button").click(function(){
        $(".fc-view-month .fc-event").hide();
    })
    $(".fc-agendaDay-button, .fc-today-button").click(function(){
        var date = $('#calendar').fullCalendar('getDate');
        if ($(this).className === 'fc-today-button') {
            var date = moment(new Date());
        }
        var resources = getResources(date);
        refreshCalendar(resources, date);
    })
        addAvailabilityEvents();
}

 
</script>
