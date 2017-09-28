<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
use common\models\User;
use common\models\LocationAvailability;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' =>'<i title="Edit" class="fa fa-pencil edit-lesson-schedule"></i>',
    'title' => 'Schedule',
	'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Teacher</dt>
	<dd>
		<a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_TEACHER, 'id' => $model->teacherId]) ?>">
		<?= $model->teacher->publicIdentity; ?>
	</a></dd>
	<dt>Date</dt>
	<dd><?= (new \DateTime($model->date))->format('l, F jS, Y'); ?></dd>
	<dt>Time</dt>
	<dd><?= Yii::$app->formatter->asTime($model->date); ?></dd>
	<dt>Duration</dt>
	<dd><?= (new \DateTime($model->duration))->format('H:i'); ?></dd>
	<?php if($model->isUnscheduled()) : ?>
		<dt>Expiry Date</dt>
		<dd><?= Yii::$app->formatter->asDate($model->privateLesson->expiryDate); ?></dd>
	<?php endif; ?>
</dl>
<?php LteBox::end() ?>
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
     $(document).ready(function() {
    $(document).on('click', '.lesson-schedule-cancel', function () {
		$('#lesson-schedule-modal').modal('hide');
		return false;
	});
	$(document).on('click', '.edit-lesson-schedule', function () {
    	$('#spinner').hide();
		$('#lesson-schedule-modal').modal('show');
        refreshcalendar.refresh();
		return false;
	});

    var calendar = {
		load : function(events,availableHours) {
			//var teacherId = $('#lesson-teacherid').val();
			//var params = $.param({teacherId: teacherId});
		   //$('#teacher-lesson').fullCalendar('destroy');
            $('#lesson-edit-calendar').fullCalendar({
            	schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                //defaultDate: date,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'agendaWeek'
                },
                allDaySlot: false,
                slotDuration: '00:15:00',
                titleFormat: 'DD-MMM-YYYY, dddd',
                defaultView: 'agendaWeek',
                minTime: "<?php echo $from_time; ?>",
                maxTime: "<?php echo $to_time; ?>",
                overlapEvent: false,
                overlapEventsSeparate: true,
                events: events,
//                select: function (start, end, allDay) {
//                    $('#extra-lesson-date').val(moment(start).format('YYYY-MM-DD hh:mm A'));
//                    $('#lesson-calendar').fullCalendar('removeEvents', 'newEnrolment');
//					var duration = $('#lesson-duration').val();
//					var endtime = start.clone();
//					var durationMinutes = moment.duration(duration).asMinutes();
//					moment(endtime.add(durationMinutes, 'minutes'));
//					
//                    $('#lesson-calendar').fullCalendar('renderEvent',
//                        {
//                            id: 'newEnrolment',
//                            start: start,
//                            end: endtime,
//                            allDay: false
//                        },
//                    true // make the event "stick"
//                    );
//                    $('#lesson-calendar').fullCalendar('unselect');
//                },
                eventAfterAllRender: function (view) {
                    $('#spinner').hide(); 
                    $('.fc-short').removeClass('fc-short');
                },
                selectable: true,
                selectHelper: true,
            });
		}
	};
var refreshcalendar = {
        refresh : function(){
            var events, availableHours;
            var teacherId = $('#lesson-teacherid').val();
            $('#lesson-schedule-modal .modal-dialog').css({'width': '1000px'});
                $.ajax({
                    url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
                    type: 'get',
                    dataType: "json",
                    success: function (response)
                    {
                        events = response.events;
                        availableHours = response.availableHours;
                        calendar.load(events,availableHours);
                    }
                });
            }
        };
        $(document).on('change', '#lesson-teacher', function () {
            refreshcalendar.refresh();
            return false;
        });
     });
    </script>
