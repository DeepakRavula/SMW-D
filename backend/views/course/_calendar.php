<?php

use common\models\LocationAvailability;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php $this->render('/lesson/_color-code'); ?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
 <div class="row-fluid">
	<div id="course-calendar">
    <div id="spinner" class="spinner" style="display:none;">
    <img src="/backend/web/img/page-loader.gif" alt="" height="100" width="100"/>
</div>
    </div>
</div>
 <div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Apply'), ['class' => 'btn btn-primary course-apply', 'name' => 'button']) ?>
	<?= Html::a('Cancel', '#', ['class' => 'btn btn-default course-cancel']);
	?>
	<div class="clearfix"></div>
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

<script>
$(document).ready(function(){
	$(document).on('click', '.course-apply, .course-cancel', function (e) {
		$('#course-calendar-modal').modal('hide');
		return false;
	});
	$(document).on('click', '.course-calendar-icon', function() {
        $('#course-calendar').fullCalendar('destroy');
		$('#course-calendar-modal').modal('show');
        $('#course-calendar-modal .modal-dialog').css({'width': '1000px'});
		var date = moment(new Date()).format('DD-MM-YYYY');
	    renderCalendar(date, this);
	});
   	$(document).on('change', '#course-teacherid', function() {
		$('.remove-item').click();
		$('.day').val('');
	    $('.time').val('');
		return false;
	}); 
    function renderCalendar(date, lessonFreeSlotPicker) {
        var events, availableHours;
        var teacherId = $('#course-teacherid').val();
        $('#spinner').show();
        $.ajax({
            url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                events = response.events;
                availableHours = response.availableHours;
                refreshCalendar(availableHours, events, date, lessonFreeSlotPicker);
                $('#spinner').hide();
            }
        });
    }

    function refreshCalendar(availableHours, events, date, lessonFreeSlotPicker) {
        $('#course-calendar').fullCalendar('destroy');
        $('#course-calendar').fullCalendar({
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
				$(lessonFreeSlotPicker).parent().find('.lesson-time').find('.time').val(moment(start).format('DD-MM-YYYY h:mm A'));
				$(lessonFreeSlotPicker).parent().find('.lesson-day').find('.day').val(moment(start).format('dddd'));
				var duration = $('#course-duration').val();
                $('#course-calendar').fullCalendar('removeEvents', 'newEnrolment');
				var endtime = start.clone();
                var durationMinutes = moment.duration(duration).asMinutes();
                moment(endtime.add(durationMinutes, 'minutes'));
                $('#course-calendar').fullCalendar('renderEvent',
                    {
                        id: 'newEnrolment',
                        start: start,
                        end: endtime,
                        allDay: false
                    },
                true // make the event "stick"
                );
                $('#course-calendar').fullCalendar('unselect');
            },
            loading: function () { 
            $('#spinner').show(); 
            },
            eventAfterAllRender: function (view) {
                $('.fc-short').removeClass('fc-short');
                $('#spinner').hide();                
            },
            selectable: true,
            selectHelper: true,
        });
    }
});
</script>
