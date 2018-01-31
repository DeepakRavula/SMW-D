<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
use common\models\Location;
use common\models\User;
use common\models\LocationAvailability;

?>
<?php
$toolBoxHtml = $this->render('_button', [
    'model' => $model,
]);
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => $toolBoxHtml,
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
<?php if ($model->rootLesson) : ?>
    <dt>Original Date</dt>
    <dd><?= (new \DateTime($model->rootLesson->date))->format('l, F jS, Y'); ?></dd>
<?php endif; ?>
    <dt>Date</dt>
    <dd><?= (new \DateTime($model->date))->format('l, F jS, Y'); ?></dd>
    <dt>Time</dt>
    <dd><?= Yii::$app->formatter->asTime($model->date); ?></dd>
    <dt>Duration</dt>
    <dd><?= (new \DateTime($model->duration))->format('H:i'); ?></dd>
    <dt>Expiry Date</dt>
    <dd><?= Yii::$app->formatter->asDate($model->privateLesson->expiryDate); ?></dd>

</dl>
<?php LteBox::end() ?>
<?php
$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
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
    $(document).ready(function () {
        $(document).on('click', '.lesson-schedule-cancel', function () {
            $('#lesson-schedule-modal').modal('hide');
            return false;
        });
        $(document).on('click', '.edit-lesson-schedule', function () {
            $('#lesson-schedule-modal').modal('show');
            refreshcalendar.refresh();
            return false;
        });

        var calendar = {
            load: function (events, availableHours, date) {
                //var teacherId = $('#lesson-teacherid').val();
                //var params = $.param({teacherId: teacherId});
                $('#lesson-edit-calendar').fullCalendar('destroy');
                $('#lesson-edit-calendar').fullCalendar({
                    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                    defaultDate: date,
					firstDay : 1,
		            nowIndicator: true,
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right:'',
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
                    overlapEvent: false,
                    overlapEventsSeparate: true,
                    events: events,
                    select: function (start, end, allDay) {
                        $('#lesson-date').val(moment(start).format('DD-MM-YYYY hh:mm A'));
                        $('#lesson-edit-calendar').fullCalendar('removeEvents', 'newEnrolment');
                        var duration = $('#course-duration').val();
                        var endtime = start.clone();
                        var durationMinutes = moment.duration(duration).asMinutes();
                        moment(endtime.add(durationMinutes, 'minutes'));

                        $('#lesson-edit-calendar').fullCalendar('renderEvent',
                                {
                                    id: 'newEnrolment',
                                    start: start,
                                    end: endtime,
                                    allDay: false
                                },
                                true // make the event "stick"
                                );
                        $('#lesson-edit-calendar').fullCalendar('unselect');
                    },
                    selectable: true,
                    selectHelper: true,
                    eventAfterAllRender: function () {
                        $('.fc-short').removeClass('fc-short');
                    },
                });
            }
        };
        var refreshcalendar = {
            refresh: function () {
                var events, availableHours;
                var teacherId = $('#lesson-teacherid').val();
                var date = moment($('#lesson-date').val(), 'DD-MM-YYYY', true).format('YYYY-MM-DD');
                if (!moment(date).isValid()) {
                    var date = moment($('#lesson-date').val(), 'DD-MM-YYYY h:mm A', true).format('YYYY-MM-DD');
                }
                if (date === 'Invalid date') {
                    alert('invalid');
                    $('#lesson-calendar').fullCalendar('destroy');
                    $('#new-lesson-modal .modal-dialog').css({'width': '600px'});
                    $('.lesson-program').removeClass('col-md-4');
                    $('.lesson-teacher').removeClass('col-md-4');
                    $('.lesson-date').removeClass('col-md-4');
                } else {
                    $('.lesson-program').addClass('col-md-4');
                    $('.lesson-teacher').addClass('col-md-4');
                    $('.lesson-date').addClass('col-md-4');
                    $('#lesson-schedule-modal .modal-dialog').css({'width': '1000px'});
                    $.ajax({
                        url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
                        type: 'get',
                        dataType: "json",
                        success: function (response)
                        {
                            events = response.events;
                            availableHours = response.availableHours;
                            $('#loadingspinner').hide();
                        calendar.load(events,availableHours,date);
                        }
                    });
                }
            }
        };
        $(document).on('change', '#lesson-teacherid', function () {
            refreshcalendar.refresh();
        });
        $(document).on('beforeSubmit', '#lesson-edit-form', function (e) {
            $.ajax({
                url: '<?= Url::to(['lesson/update', 'id' => $model->id]); ?>',
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#lesson-schedule-modal').modal('hide');
                        window.location.href = response.url;
                    } else
                    {
                        $('#error').html(response.errors).fadeIn().delay(7000).fadeOut();
                    }
                }
            });
            return false;
        });

    });
</script>
