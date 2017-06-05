<?php

use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use common\models\CalendarEventColor;

$this->title = 'Schedule for ' .(new \DateTime())->format('l, F jS, Y');
?>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<?php $this->render('_color-code'); ?>
<div class="clearfix"></div>
<div id='calendar'></div>
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
</script>
