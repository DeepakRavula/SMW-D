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
<script type="text/javascript" src="/plugins/poshytip/jquery.poshytip.min.js"></script>
<script type="text/javascript" src="/plugins/poshytip/jquery.poshytip.js"></script>
<link type="text/css" href="/plugins/poshytip/tip-darkgray/tip-darkgray.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-green/tip-green.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-skyblue/tip-skyblue.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-twitter/tip-twitter.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-violet/tip-violet.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-yellow/tip-yellow.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-yellowsimple/tip-yellowsimple.css" rel='stylesheet' />

<style>
@media (max-width: 768px){
 #calendar .fc-view .fc-time-grid-container,#calendar .fc-day-grid-container{
	 overflow-x:auto !important;
	 overflow-y: hidden !important;
	 min-height:220px !important;
	 height: auto !important;
 }
 #calendar .fc-center h2{
	     font-size: 16px;
    margin-top: 10px;
 }
 .wrap > .container {
    padding: 47px 7px 20px;
}
 #calendar .fc-header-toolbar .fc-left button{
	 padding:0 .4em;
 }
 #calendar .fc-toolbar{
	     padding: 10px 0;
 }
}

</style>
<?php $this->render('_color-code'); ?>
<div class="clearfix"></div>
<div id='calendar'></div>
<?php $userId = Yii::$app->user->id;?>
<script type="text/javascript">
var locationAvailabilities   = <?php echo Json::encode($locationAvailabilities); ?>;
var userId = '<?php echo $userId; ?>';
$(document).ready(function() {
    var params = $.param({ userId: userId });
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
        events: {
            url: '<?= Url::to(['schedule/render-day-events']) ?>?' + params,
            type: 'GET',
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
		},
		allDaySlot:false,
    });
});
</script>
