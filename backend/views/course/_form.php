<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use common\models\Program;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\TeacherAvailability;
use yii\data\ActiveDataProvider;
use common\models\LocationAvailability;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php
	$query = TeacherAvailability::find()
                ->joinWith('userLocation')
                ->where(['user_id' => key($teacher)]);
        $teacherDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]); ?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="group-course-form p-10">
	<?php
	$form			 = ActiveForm::begin([
                        'id' => 'group-course-form',
			'enableAjaxValidation' => true,
			'enableClientValidation' => false
	]);
	?>
	<div class="row p-10">
            <div class="col-md-4">
                    <?php
                    echo $form->field($model, 'programId')->dropDownList(
                            ArrayHelper::map(Program::find()->active()
                                            ->where(['type' => Program::TYPE_GROUP_PROGRAM])
                                            ->all(), 'id', 'name'), ['prompt' => 'Select Program'])
                    ?>
            </div>
            <div class="col-md-4">
                <?php echo $form->field($model, 'teacherId')->dropDownList($teacher) ?>
            </div>
            <div class="col-md-4">
                <?php echo $form->field($model, 'endDate')->widget(DatePicker::classname(), [
                    'options' => [
                        'value' =>Yii::$app->formatter->asDate((new \DateTime())->format('d-m-Y')),
                    ],
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy',
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-12">
                <div id="group-course-calendar"> </div>
            </div>
            <div class="col-md-3">
                    <?php
                    echo $form->field($model, 'duration')->hiddenInput()->label(false);
                    ?>
            </div>
            <div class="col-md-3">
                    <?php echo $form->field($model, 'day')->hiddenInput()->label(false); ?>
            </div>
            <div class="col-md-3">
                    <?= $form->field($model, 'fromTime')->hiddenInput()->label(false); ?>
            </div>
            <div class="col-md-3">
                    <?= $form->field($model, 'startDate')->hiddenInput()->label(false); ?>
            </div>
        </div>
    <div class="form-group p-l-10">
<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'),
	['id' => 'group-course-save', 'class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
<?php
if (!$model->isNewRecord) {
	echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
}
?>
    </div>
<?php ActiveForm::end(); ?>
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

<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<script>
$(document).ready(function(){
    var date = moment(new Date()).format('DD-MM-YYYY');
    renderCalendar(date);
    $(document).on('change', '#course-teacherid', function () {
        var date = $('#group-course-calendar').fullCalendar('getDate');
        renderCalendar(date);
    });

    function renderCalendar(date) {
        var events, availableHours;
        var teacherId = $('#course-teacherid').val();
        $.ajax({
            url: '/teacher-availability/availability-with-events?id=' + teacherId,
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
        $('#group-course-calendar').fullCalendar('destroy');
        $('#group-course-calendar').fullCalendar({
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
            overlapEvent: false,
            overlapEventsSeparate: true,
            events: events,
            select: function (start, end, allDay) {
                $('#course-day').val(moment(start).day());
                $('#course-startdate').val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
                $('#course-fromtime').val(moment(start).format('HH:mm:ss'));
                $('#group-course-calendar').fullCalendar('removeEvents', 'newEnrolment');
                var endtime = start.clone();
                var differenceInMinute = moment(end).minute() - moment(start).minute();
                if (differenceInMinute === 15) {
                    moment(endtime.add(30, 'minutes'));
                } else {
                    endtime = end;
                }
                var duration = moment.utc(moment(endtime, "HH:mm:ss").diff(moment(start, "HH:mm:ss"))).format("HH:mm:ss");
                $('#course-duration').val(duration);
                $('#group-course-calendar').fullCalendar('renderEvent',
                    {
                        id: 'newEnrolment',
                        start: start,
                        end: endtime,
                        allDay: false
                    },
                true // make the event "stick"
                );
                $('#group-course-calendar').fullCalendar('unselect');
            },
            eventAfterAllRender: function (view) {
                $('.fc-short').removeClass('fc-short');
            },
            selectable: true,
            selectHelper: true,
        });
    }

    $('#group-course-form').on('beforeSubmit', function (e) {
        var courseDay = $('#course-day').val();
        if( ! courseDay) {
            $('#error-notification').html("Please choose a day in the calendar").fadeIn().delay(3000).fadeOut();
            $(window).scrollTop(0);
            return false;
        }
    });
});
</script>