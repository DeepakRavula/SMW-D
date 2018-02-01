<?php
use common\models\LocationAvailability;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify">
<?php $form = ActiveForm::begin([
            'id' => 'modal-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validationUrl' => Url::to(['lesson/validate-on-update', 'id' => $model->id, 'teacherId' => null]),
            'action' => Url::to(['lesson/update', 'id' => $model->id]),
            'options' => [
                'class' => 'p-10',
            ]
        ]); ?>
    <div class="row">
        <div class="col-md-2">
            <?php
            echo $form->field($model, 'duration')->widget(
            TimePicker::classname(),
                [
                'options' => ['id' => 'course-duration'],
                'pluginOptions' => [
                    'showMeridian' => false,
                ],
            ]
        );
            ?>
        </div>
        <div class="col-md-3">
            <?php
            // Dependent Dropdown
            echo $form->field($model, 'teacherId')->widget(
                Select2::classname(),
                [
                'data' => ArrayHelper::map(User::find()
                        ->teachers(
                            $model->course->program->id,
                            Location::findOne(['slug' => \Yii::$app->location])->id
                        )
                        ->join(
                            'LEFT JOIN',
                            'user_profile',
                            'user_profile.user_id = ul.user_id'
                        )
                        ->notDeleted()
                        ->orderBy(['user_profile.firstname' => SORT_ASC])
                        ->all(), 'id', 'userProfile.fullName'),
                'options' => [
                    'id' => 'lesson-teacherid'
                ]
                ]
            )->label('Teacher');
            ?>  
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'date', [
                'inputTemplate' => '<div class="input-group">{input}<span class="input-group-addon" title="Clear field">
                    <span class="glyphicon glyphicon-remove"></span></span></div>'
                ])->textInput([
                    'readonly' => true,
                    'value' => Yii::$app->formatter->asDateTime($model->date)
                ])->label('Reschedule Date');
            ?>  
        </div>
        <div class="col-md-3">
            <?= $form->field($privateLessonModel, 'expiryDate')->widget(
                    DatePicker::classname(),
                    [
                    'options' => [
                        'value' => Yii::$app->formatter->asDate($privateLessonModel->expiryDate),
                    ],
                    'layout' => '{input}{picker}',
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy',
                    ],
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="error" style="display:none;" class="alert-danger alert fade in"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="lesson-edit-calendar">
                <div id="loadingspinner" class="spinner" style="" >
                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                    <span class="sr-only">Loading...</span>
                </div>  
            </div>
        </div>
    </div>
        <?php ActiveForm::end(); ?>
</div>
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

<script type="text/javascript">
    var calendar = {
        load: function (events, availableHours, date) {
            $('#lesson-edit-calendar').fullCalendar('destroy');
            $('#lesson-edit-calendar').fullCalendar({
                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                defaultDate: date,
                firstDay : 1,
                nowIndicator: true,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right:''
                },
                height: 500,
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
                    $('#lesson-date').val(moment(start).format('DD-MM-YYYY hh:mm A')).trigger('change');
                    $('#lesson-edit-calendar').fullCalendar('removeEvents', 'newEnrolment');
                    var duration = $('#course-duration').val();
                    var endtime = start.clone();
                    var durationMinutes = moment.duration($.isEmptyObject(duration) ? '00:30' : duration).asMinutes();
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
                }
            });
        }
    };
    var refreshcalendar = {
        refresh: function () {
            var events, availableHours;
            var teacherId = $('#lesson-teacherid').val();
            var date = moment($('#lesson-date').val(), 'DD-MM-YYYY h:mm A', true).format('YYYY-MM-DD');
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
    };
    
    $('#popup-modal').on('shown.bs.modal', function () {
        refreshcalendar.refresh();
    });

    $(document).on('change', '#lesson-teacherid', function () {
        refreshcalendar.refresh();
    });
    
    $(document).on('click', '.glyphicon-remove', function () {
        $('#lesson-date').val('').trigger('change');
    });
    
    $(document).on('modal-success', function(event, params) {
        window.location.href = params.url;
        return false;
    });
</script>
