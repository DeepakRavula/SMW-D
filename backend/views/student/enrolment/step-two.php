<?php

use common\models\LocationAvailability;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="col-md-4">
	<?php
    // Dependent Dropdown
    echo $form->field($model, 'teacherId')->widget(DepDrop::classname(),
        [
        'options' => ['id' => 'course-teacherid'],
        'pluginOptions' => [
            'depends' => ['course-programid'],
            'placeholder' => 'Select...',
            'url' => Url::to(['course/teachers']),
        ],
    ]);
    ?>
</div>
<div class="col-md-4">
	<?= $form->field($courseSchedule, 'day')->hiddenInput()->label(false) ?>
</div>
<div class="col-md-4">
<?= $form->field($courseSchedule, 'fromTime')->hiddenInput()->label(false) ?>
</div>
<div class="clearfix"></div>
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
    function refreshCalendar(availableHours, events, date) {
        $('#calendar').fullCalendar('destroy');
        $('#calendar').fullCalendar({
    		defaultDate: moment(date, 'DD-MM-YYYY', true).format('YYYY-MM-DD'),
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
            selectConstraint: 'businessHours',
            eventConstraint: 'businessHours',
            businessHours: availableHours,
            allowCalEventOverlap: true,
            overlapEventsSeparate: true,
            events: events,
            select: function (start, end, allDay) {
                $('#calendar').fullCalendar('removeEvents', 'newEnrolment');
                $('#courseschedule-day').val(moment(start).format('dddd'));
                $('#courseschedule-fromtime').val(moment(start).format('h:mm A'));
                $('#course-startdate').val(moment(start).format('DD-MM-YYYY h:mm A'));
                var endtime = start.clone();
                var durationMinutes = moment.duration($('#courseschedule-duration').val()).asMinutes();
                moment(endtime.add(durationMinutes, 'minutes'));
                $('#calendar').fullCalendar('renderEvent',
                    {
                        id: 'newEnrolment',
                        start: start,
                        end: endtime,
                        allDay: false
                    },
                true // make the event "stick"
                );
                $('#calendar').fullCalendar('unselect');
            },
            eventAfterAllRender: function (view) {
                $('.fc-short').removeClass('fc-short');
            },
            selectable: true,
            selectHelper: true,
        });
    }
    $(document).ready(function () {
        $(document).on('change', '#course-teacherid', function () {
            var events, availableHours;
            var teacherId = $('#course-teacherid').val();
			var date = $('#course-startdate').val();
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
        });
    });
</script>