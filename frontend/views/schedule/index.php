<?php

use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\Tabs;

$this->title = 'Schedule for ' .(new \DateTime())->format('l, F jS, Y');
?>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>

<style type="text/css">
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
</div>
<?php $teacherId = Yii::$app->user->id;?>
<script type="text/javascript">
var locationAvailabilities   = <?php echo Json::encode($locationAvailabilities); ?>;
var teacherId = '<?php echo $teacherId; ?>';
$(document).ready(function() {
    var params = $.param({ teacherId: teacherId });
    $('#calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		height:'auto',
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: "<?php echo $from_time; ?>",
        maxTime: "<?php echo $to_time; ?>",
        slotDuration: "00:15:00",
        editable: false,
        droppable: false,
        events: {
            url: '<?= Url::to(['schedule/render-day-events']) ?>?' + params,
            type: 'GET',
            error: function() {
                $("#calendar").fullCalendar("refetchEvents");
            }
        },
		allDaySlot:false,
    });
});

$(document).ready(function () {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var tab  = e.target.text;
        if (tab === "Classroom View") {
            showclassroomCalendar();
            $('.calendar-filter').hide();
        } else {
            refreshCalendar();
            $('.calendar-filter').show();
        }
    });
});
function showclassroomCalendar() {
    var params = $.param({ teacherId: teacherId });
    var fromTime = "09:00:00";
    var toTime   = "17:00:00";

    $('#classroom-calendar').html('');
    $('#classroom-calendar').unbind().removeData().fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
       header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
        height: "auto",
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: fromTime,
        maxTime: toTime,
        slotDuration: "00:15:00",
        allDaySlot:false,
        editable: false,
        eventDurationEditable: false,
        resources: {
            url: '<?= Url::to(['schedule/render-classroom-resources']) ?>?' + params,
            type: 'GET',
            error: function() {
                $("#classroom-calendar").fullCalendar("refetchResources");
            }
        },
        events: {
            url: '<?= Url::to(['schedule/render-classroom-events']) ?>?' + params,
            type: 'GET',
            error: function() {
                $("#classroom-calendar").fullCalendar("refetchEvents");
            }
        },
    });
}

function refreshCalendar() {
    var params = $.param({ teacherId: '<?php echo $teacherId; ?>' });
    var minTime = "09:00:00";
    var maxTime = "17:00:00";

    $('#calendar').html('');
    $('#calendar').unbind().removeData().fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		height: "auto",
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: minTime,
        maxTime: maxTime,
        slotDuration: "00:15:00",
        allDaySlot:false,
        editable: false,
        droppable: false,
        events: {
            url: '<?= Url::to(['schedule/render-day-events']) ?>?' + params,
            type: 'GET',
            error: function() {
                $("#calendar").fullCalendar("refetchEvents");
            }
        },
    });
}
</script>
