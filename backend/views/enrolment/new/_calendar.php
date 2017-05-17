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
<div class="schedule-index">
	<div class="row schedule-filter">
		<div class="col-md-4 pull-right">
			<div id="datepicker" class="input-group date">
				<input type="text" class="form-control" value=<?=(new \DateTime())->format('d-m-Y')?>>
				<div class="input-group-addon">
					<span class="glyphicon glyphicon-calendar"></span>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="enrolment-calendar"></div>
<script type="text/javascript">
var availableTeachersDetails = <?php echo Json::encode($availableTeachersDetails); ?>;
var locationAvailabilities   = <?php echo Json::encode($locationAvailabilities); ?>;
var programId = '<?php echo $programId; ?>';
$(document).ready(function() {
    var params = $.param({ date: moment(new Date()).format('YYYY-MM-DD'),
        programId: programId});
    $('#enrolment-calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: "<?php echo $from_time; ?>",
        maxTime: "<?php echo $to_time; ?>",
        slotDuration: "00:15:00",
        editable: false,
        droppable: false,
        resources: {
            url: '<?= Url::to(['enrolment/render-resources']) ?>?' + params,
            type: 'POST',
            error: function() {
                $("#enrolment-calendar").fullCalendar("refetchResources");
            }
        },
        events: {
            url: '<?= Url::to(['enrolment/render-day-events']) ?>?' + params,
            type: 'POST',
            error: function() {
                $("#enrolment-calendar").fullCalendar("refetchEvents");
            }
        },
		allDaySlot:false,
    });
    $('#datepicker').datepicker ({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true
    });

    $('#datepicker').on('change', function(){
        var date = $('#datepicker').datepicker("getDate");
        var formattedDate = moment(date).format('dddd, MMMM Do, YYYY');
        $(".content-header h1").text("Schedule for " + formattedDate);
            refreshCalendar(moment(date));
	});
});

function refreshCalendar(date) {
	var programId = '<?= $programId;?>';
    var params = $.param({ date: moment(date).format('YYYY-MM-DD'),
        programId: programId });
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
    $('#enrolment-calendar').html('');
    $('#enrolment-calendar').unbind().removeData().fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
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
            url: '<?= Url::to(['enrolment/render-resources']) ?>?' + params,
            type: 'POST',
            error: function() {
                $("#enrolment-calendar").fullCalendar("refetchResources");
            }
        },
        events: {
            url: '<?= Url::to(['enrolment/render-day-events']) ?>?' + params,
            type: 'POST',
            error: function() {
                $("#enrolment-calendar").fullCalendar("refetchEvents");
            }
        },
    });
}
</script>
