<?php

use yii\helpers\Json;
use yii\helpers\Url;
use common\models\CalendarEventColor;
use common\models\LocationAvailability;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/* @var $this yii\web\View */

?>
<?= $this->render('/lesson/_color-code');
?>
<style type="text/css">
.box-body .fc{
    margin:0 !important;
}
.apply-button {
	margin-top:25px;
}
#datepicker {
	margin-top:25px;
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
<?php $this->render('/lesson/_color-code'); ?>
<?php $form = ActiveForm::begin(); ?>
<div class="form-group">
	<div class="col-sm-3">
		<?= $form->field($model, 'startDate')->textInput(['readOnly' => true])->label('Date & Time'); ?>
	</div>
	<div class="col-sm-2 apply-button">
		<?= Html::a('Apply', '#', ['class' => 'btn btn-info enrolment-apply-button']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<div class="clearfix"></div>

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
    $(document).on('click', '.enrolment-apply-button', function(){
		var day = $('#courseschedule-day').val(); 
		var date = $('#course-startdate').val(); 
		var time = moment(date,'DD-MM-YYYY h:mm A').format('h:mm A');
		var duration = $('#courseschedule-duration').val();
        $('#new-enrolment-modal').modal('hide');
		$('.new-enrolment-time').text(day + ', ' + time + ' & ' + duration);
		$('#courseschedule-fromtime').val(time);
	});
    $(document).on('click', '.enrolment-calendar-icon', function(){
        $('#enrolment-calendar').fullCalendar('destroy');
        $('#new-enrolment-modal').modal('show');
        $('#new-enrolment-modal .modal-dialog').css({'width': '1000px'});
        var date = moment(new Date()).format('DD-MM-YYYY');
	    renderCalendar(date);
    });
	function renderCalendar(date) {
        var events, availableHours;
        var teacherId = $('#course-teacherid').val();
        $.ajax({
            url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                events = response.events;
                availableHours = response.availableHours;
                refreshCalendar(availableHours, events, date);
            }
        });
    }

    function refreshCalendar(availableHours, events, date) {
        $('#enrolment-calendar').fullCalendar('destroy');
        $('#enrolment-calendar').fullCalendar({
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            defaultDate: moment(new Date()).format('YYYY-MM-DD'),
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'agendaWeek'
            },
            allDaySlot: false,
			height:'auto',
            slotDuration: '00:15:00',
            titleFormat: 'DD-MMM-YYYY, dddd',
            defaultView: 'agendaWeek',
            minTime: "<?php echo $from_time; ?>",
            maxTime: "<?php echo $to_time; ?>",
            selectConstraint: 'businessHours',
            eventConstraint: 'businessHours',
            businessHours: availableHours,
            allowCalEventOverlap: true,
            overlapEventsSeparate: true,
            events: events,
            select: function (start, end, allDay) {
				$('input[name="Course[startDate]"]').val(moment(start).format('DD-MM-YYYY h:mm A'));
				$('input[name="CourseSchedule[day]"]').val(moment(start).format('dddd'));
				var duration = $('#courseschedule-duration').val();
                $('#enrolment-calendar').fullCalendar('removeEvents', 'newEnrolment');
				var endtime = start.clone();
                var durationMinutes = moment.duration(duration).asMinutes();
                moment(endtime.add(durationMinutes, 'minutes'));
                $('#enrolment-calendar').fullCalendar('renderEvent',
                    {
                        id: 'newEnrolment',
                        start: start,
                        end: endtime,
                        allDay: false
                    },
                true // make the event "stick"
                );
                $('#enrolment-calendar').fullCalendar('unselect');
            },
            eventAfterAllRender: function (view) {
                $('.fc-short').removeClass('fc-short');
            },
            selectable: true,
            selectHelper: true,
        });
    }
});
</script>
