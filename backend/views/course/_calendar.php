<?php

use common\models\LocationAvailability;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

?>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
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
<div class="row-fluid">
	<div id="calendar" ></div>
</div>
<?php
$locationId = Yii::$app->session->get('location_id');
$minLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['fromTime' => SORT_ASC])
    ->one();
$maxLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['toTime' => SORT_DESC])
    ->one();
$from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
$to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>
<script type="text/javascript">
$(document).ready(function() {
  var params = $.param({ date: moment(new Date()).format('YYYY-MM-DD')});
    $('#calendar').fullCalendar({
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
            url: '<?= Url::to(['course/render-resources']) ?>?' + params,
            type: 'POST',
            error: function() {
                alert('There was an error while fetching resources, Please re-select!');
            }
        },
        events: {
            url: '<?= Url::to(['course/render-day-events']) ?>?' + params,
            type: 'POST',
            error: function() {
                alert('There was an error while fetching events, Please re-select!');
            }
        },
		allDaySlot:false,
        eventClick: function(event) {
            $(location).attr('href', event.url);
        }
    });
});
</script>


