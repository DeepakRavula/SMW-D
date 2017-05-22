<?php

use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use common\models\CalendarEventColor;

use wbraganca\selectivity\SelectivityWidget;
use yii\helpers\ArrayHelper;
use common\models\Program;

/* @var $this yii\web\View */

$this->title = 'Schedule for ' .(new \DateTime())->format('l, F jS, Y');
?>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<script type="text/javascript" src="/plugins/poshytip/jquery.poshytip.min.js"></script>
<script type="text/javascript" src="/plugins/poshytip/jquery.poshytip.js"></script>
<link type="text/css" href="/plugins/poshytip/tip-darkgray/tip-darkgray.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-green/tip-green.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-skyblue/tip-skyblue.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-twitter/tip-twitter.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-violet/tip-violet.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-yellow/tip-yellow.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-yellowsimple/tip-yellowsimple.css" rel='stylesheet' />
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
<style type="text/css">
    .schedule-index {
        position: absolute;
        top: -45px;
    }
.selectivity-single-select{
    margin-right: 10px;
    padding-bottom:2px;
}
.tab-content{
    padding:0 !important;
}
.box-body .fc{
    margin:0 !important;
}
.qtip{
    }
.ui-widget-content{
    font-size: 12px;
    line-height: 20px;
    overflow: inherit;
    color: #333333;
    padding: 10px;
    background-color: #ffffff;
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
    background-image: none;
    text-transform: capitalize;
    position: absolute;
    top: -182px;
    width: 150px;
    border: none;
}
.ui-widget-content b{
	display:block;
	color:#ff0000;
	font-size:13px;
	font-weight:400;
	border-top:1px solid #ccc;
	padding-top:5px;
	padding-bottom:0;
}
.ui-widget-content b:first-child{
	padding:0;
	border:none;
}
.ui-widget-content:before{
	content:"";
	width: 0;
height: 0;
border-style: solid;
border-width: 10px 10px 0 10px;
border-color: #fff transparent transparent transparent;
position:absolute;
left:45%;
bottom:-10px;
}	
</style>
<div class=" calendar-filter">
        <div class="pull-right m-1-20">
		<span class="filter_by_calendar">Filter by</span>
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
<div class="tabbable-panel">
    <div class="tabbable-line">
        <?php

        $teacher = $this->render('_teacher-view',[
            'availableTeachersDetails' => $availableTeachersDetails
        ]);

        $classroom = $this->render('_classroom-view');

        ?>

        <?php echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Teacher View',
                    'content' => $teacher,
                    'options' => [
                            'id' => 'teacher-view',
                        ],
                ],
                [
                    'label' => 'Classroom View',
                    'content' => $classroom,
                    'options' => [
                            'id' => 'classroom-view',
                        ],
                ],
            ],
        ]);?>
    </div>
    <div class="schedule-index">
        <div class="row schedule-filter">
            <div class="col-md-2 pull-right">
                <div id="datepicker" class="input-group date">
                    <input type="text" class="form-control" value=<?=(new \DateTime())->format('d-m-Y')?>>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var availableTeachersDetails = <?php echo Json::encode($availableTeachersDetails); ?>;
var locationAvailabilities   = <?php echo Json::encode($locationAvailabilities); ?>;
$(document).ready(function() {
    var params = $.param({ date: moment(new Date()).format('YYYY-MM-DD'),
        programId: '',
        teacherId: '' });
    $('#calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
		height:'auto',
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: "<?php echo $from_time; ?>",
        maxTime: "<?php echo $to_time; ?>",
        slotDuration: "00:15:00",
        editable: false,
        droppable: false,
        resources: {
            url: '<?= Url::to(['schedule/render-resources']) ?>?' + params,
            type: 'POST',
            error: function() {
                $("#calendar").fullCalendar("refetchResources");
            }
        },
        events: {
            url: '<?= Url::to(['schedule/render-day-events']) ?>?' + params,
            type: 'POST',
            error: function() {
                $("#calendar").fullCalendar("refetchEvents");
            }
        },
		allDaySlot:false,
        eventClick: function(event) {
            $(location).attr('href', event.url);
        },
		eventRender: function(event, element) {
			element.poshytip({
				className: 'tip-yellowsimple',
				alignTo: 'cursor',
				alignX: 'center',
				alignY : 'top',
				offsetY: 5,
				followCursor: false,
				slide: false,
				content : function(updateCallback) {
					return event.description;
				}
			});
		}
    });
});

$(document).ready(function () {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var tab  = e.target.text;
        var date = $('#datepicker').datepicker("getDate");
        if (tab === "Classroom View") {
            showclassroomCalendar(moment(date));
            $('.calendar-filter').hide();
        } else {
            refreshCalendar(moment(date));
            $('.calendar-filter').show();
        }
    });
});

function loadTeachers(program) {
    var teachers = [];
    if((program == 'undefined') || (program == null)) {
        $.each( availableTeachersDetails, function( key, value ) {
            value.text = value.name;
            teachers.push(value);
        });
    }else {
        $.each( availableTeachersDetails, function( key, value ) {
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
    $('#datepicker').datepicker ({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true
    });
    
    setTimeout(function(){
	$('#program-selector').on('change', function(e){
        var date = $('#calendar').fullCalendar('getDate');
        refreshCalendar(moment(date));
        loadTeachers(e.value);
	}); }, 3000);

    setTimeout(function(){
	$('#teacher-selector').on('change', function(){
        var date = $('#calendar').fullCalendar('getDate');
        refreshCalendar(moment(date));
	}); }, 3000);

    $('#datepicker').on('change', function(){
        var date = $('#datepicker').datepicker("getDate");
        var formattedDate = moment(date).format('dddd, MMMM Do, YYYY');
        $(".content-header h1").text("Schedule for " + formattedDate);
		if ($('.nav-tabs .active').text() === 'Classroom View') {
            showclassroomCalendar(moment(date));
        } else {
            refreshCalendar(moment(date));
        }
	});
});

function showclassroomCalendar(date) {
    var params   = $.param({ date: moment(date).format('YYYY-MM-DD') });
    var fromTime = "09:00:00";
    var toTime   = "17:00:00";
    var day      = moment(date).day();
    $.each( locationAvailabilities, function( key, value ) {
        if (day === 0) {
            day = 7;
        }
        if (day === value.day) {
            fromTime = value.fromTime;
            toTime   = value.toTime;
        }
    });
    $('#classroom-calendar').html('');
    $('#classroom-calendar').unbind().removeData().fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
		height: "auto",
        defaultDate: date,
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: fromTime,
        maxTime: toTime,
        slotDuration: "00:15:00",
        allDaySlot:false,
        editable: true,
        droppable: false,
        resources: {
            url: '<?= Url::to(['schedule/render-classroom-resources']) ?>?' + params,
            type: 'POST',
            error: function() {
                $("#classroom-calendar").fullCalendar("refetchResources");
            }
        },
        events: {
            url: '<?= Url::to(['schedule/render-classroom-events']) ?>?' + params,
            type: 'POST',
            error: function() {
                $("#classroom-calendar").fullCalendar("refetchEvents");
            }
        },
  		eventRender: function(event, element) {
			element.poshytip({
				className: 'tip-yellowsimple',
				alignTo: 'cursor',
				alignX: 'center',
				alignY : 'top',
				offsetY: 5,
				followCursor: false,
				slide: false,
				content : function(updateCallback) {
					return event.description;
				}
			});
		},
        eventDrop: function(event) {
            var params = $.param({
                id: event.id,
                classroomId: event.resourceId
            });
            $.ajax({
                url: '<?= Url::to(['lesson/modify-classroom']); ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status) {
                        $("#classroom-calendar").fullCalendar("refetchEvents");
                    } else {
                        $('#notification').html(response.errors).fadeIn().delay(5000).fadeOut();
                        $("#classroom-calendar").fullCalendar("refetchEvents");
				 		$(window).scrollTop(0);
						
                    }
                }
            });
        }
    });
}

function refreshCalendar(date) {
    var params = $.param({ date: moment(date).format('YYYY-MM-DD'),
        programId: $('#program-selector').selectivity('value'),
        teacherId: $('#teacher-selector').selectivity('value') });
    var minTime = "09:00:00";
    var maxTime = "17:00:00";
    var day     = moment(date).day();
    $.each( locationAvailabilities, function( key, value ) {
        if (day === 0) {
            day = 7;
        }
        if (day === value.day) {
            minTime = value.fromTime;
            maxTime = value.toTime;
        }
    });
    $('#calendar').html('');
    $('#calendar').unbind().removeData().fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
		height: "auto",
        defaultDate: date,
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: minTime,
        maxTime: maxTime,
        slotDuration: "00:15:00",
        allDaySlot:false,
        editable: false,
        droppable: false,
        resources: {
            url: '<?= Url::to(['schedule/render-resources']) ?>?' + params,
            type: 'POST',
            error: function() {
                $("#calendar").fullCalendar("refetchResources");
            }
        },
        events: {
            url: '<?= Url::to(['schedule/render-day-events']) ?>?' + params,
            type: 'POST',
            error: function() {
                $("#calendar").fullCalendar("refetchEvents");
            }
        },
		eventRender: function(event, element) {
			element.poshytip({
				className: 'tip-yellowsimple',
				alignTo: 'cursor',
				alignX: 'center',
				alignY : 'top',
				offsetY: 5,
				followCursor: false,
				slide: false,
				content : function(updateCallback) {
					return event.description;
				}
			});
		}
    });
}
</script>
