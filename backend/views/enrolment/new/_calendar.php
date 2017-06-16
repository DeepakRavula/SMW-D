<?php

use yii\helpers\Json;
use yii\helpers\Url;
use common\models\CalendarEventColor;
use common\models\LocationAvailability;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/* @var $this yii\web\View */

?>
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
            background-color: " . $rescheduledLesson->code . " !important; }"
    );
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
<?php $form = ActiveForm::begin(); ?>
<div class="form-group">
	<div class="col-sm-2">
		<?= $form->field($model, 'teacherName')->textInput(['readOnly' => true])->label('Teacher'); ?>
	</div>
	<div class="col-sm-3">
		<?= $form->field($model, 'startDate')->textInput(['readOnly' => true])->label('Date & Time'); ?>
	</div>
	<div class="col-sm-2">
		<?= $form->field($model, 'day')->textInput(['readOnly' => true])->label('Duration'); ?>
	</div>
	<div class="col-sm-2 apply-button">
		<?= Html::a('Apply', '#', ['class' => 'btn btn-info enrolment-apply-button']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<div class="col-md-3 pull-right">
	<div id="datepicker" class="input-group date">
		<input type="text" class="form-control" value=<?= (new \DateTime())->format('d-m-Y') ?>>
		<div class="input-group-addon">
			<span class="glyphicon glyphicon-calendar"></span>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<div id="enrolment-calendar"></div>

<?php
	$locationId = Yii::$app->session->get('location_id');
	$locationAvailabilities = LocationAvailability::find()
		->where(['locationId' => $locationId])
		->all();
	$locationAvailability = LocationAvailability::findOne(['locationId' => $locationId,
		'day' => (new \DateTime())->format('N')]);
	if (empty($locationAvailability)) {
		$from_time = LocationAvailability::DEFAULT_FROM_TIME;
		$to_time   = LocationAvailability::DEFAULT_TO_TIME;
	} else {
		$from_time = $locationAvailability->fromTime;
		$to_time   = $locationAvailability->toTime;
	}
	?>
<script type="text/javascript">
var locationAvailabilities   = <?php echo Json::encode($locationAvailabilities); ?>;
$(document).ready(function() {
    $(document).on('click', '.enrolment-apply-button', function(){
		var teacherName = $('#course-teachername').val(); 
		var day = $('#course-day').val(); 
		var date = $('#course-startdate').val(); 
		var time = moment(date,'DD-MM-YYYY h:mm A').format('h:mm A');
		var duration = $('#course-duration').val();
        $('#new-enrolment-modal').modal('hide');
		$('.new-enrolment-teacher').text(teacherName);
		$('.new-enrolment-time').text(day + ', ' + time + ' & ' + duration);
		$('#course-fromtime').val(time);
		$('#course-startdate').val(moment(date,'DD-MM-YYYY h:mm A').format('DD-MM-YYYY'));
	});
    $(document).on('click', '.enrolment-calendar-icon', function(){
        $('#new-enrolment-modal').modal('show');
        $('#new-enrolment-modal .modal-dialog').css({'width': '1000px'});
        setTimeout(function () {
            var date = new Date();
            refreshCalendar(moment(date));
        }, 200);
        
    });
    $('#datepicker').datepicker ({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true
    });

    $('#datepicker').on('change', function(){
        var date = $('#datepicker').datepicker("getDate");
        refreshCalendar(moment(date));
    });

    function refreshCalendar(date) {
        var programId = $('#course-programid').val();
        var params = $.param({ date: moment(date).format('YYYY-MM-DD'),
            programId: programId });
        var minTime = "<?php echo $from_time; ?>";
        var maxTime = "<?php echo $to_time; ?>";
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
			height: 'auto',
            minTime: minTime,
            maxTime: maxTime,
            slotDuration: "00:15:00",
            allDaySlot:false,
            editable: false,
            droppable: false,
            selectable:true,
			selectHelper:true,
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
            select: function(start, end, jsEvent, view, resource) {
				$('input[name="Course[startDate]"]').val(moment(start).format('DD-MM-YYYY h:mm A'));
				$('input[name="Course[day]"]').val(moment(start).format('dddd'));
                $('#course-teacherid').val(resource.id);
                $('#course-teachername').val(resource.title);
                var endtime = start.clone();
                var durationMinutes = moment.duration($('#course-duration').val()).asMinutes();
                moment(endtime.add(durationMinutes, 'minutes'));
                eventData = {
                    start: start,
                    end: endtime
                };
            }
        });
    }
});
</script>
