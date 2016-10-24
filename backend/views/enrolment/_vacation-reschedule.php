<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use common\models\Course;
use common\models\Location;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'Vacation Reschedule';
?>
<?= $this->render('_view-enrolment',[
	    'model' => $model->enrolment,
]);?>
<div class="enrolment-form form-well form-well-smw">
	<?php $form = ActiveForm::begin(); ?>
    <div class="row">
		<div class="col-md-4">
			<?php echo $form->field($model, 'lessonFromDate')->widget(DatePicker::classname(), [
               		'options' => [
                    'value' => (new \DateTime())->format('d-m-Y'),
               ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
			  ]); ?>
		</div>
        <div class="col-md-4">
			<?php echo $form->field($model, 'lessonToDate')->widget(DatePicker::classname(), [
               		'options' => [
                    'value' => $lastLessonDate->format('d-m-Y'),
               ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
			  ]); ?>
		</div>
        <div class="col-md-4">
			<?php echo $form->field($model, 'rescheduleBeginDate')->widget(DatePicker::classname(), [
               		'options' => [
                        'id' => 'reschedule-date',
                        'value' => (new \DateTime())->format('d-m-Y'),
               ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
			  ]); ?>
		</div>
		</div>
    <div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
</div>
<div id="course-detail" class="row">
		<div class="col-md-4">
        	<?= $form->field($model, 'day')->hiddenInput()->label(false)?>
		</div>
		<div class="col-md-4">
        	<?= $form->field($model, 'fromTime')->hiddenInput()->label(false)?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'startDate')->widget(DatePicker::classname(), [
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
       				'pluginOptions' => [
					'format' => 'dd-mm-yyyy',
					'todayHighlight' => true,
					'autoclose' => true
				]
			])->hiddenInput()->label(false);
			?>
		</div>
	</div>
<div id="calendar" class="row">
    <?php echo $this->render('_calendar', [
            'teacherDetails' => $teacherDetails,
    ]) ?>
</div>

<?php ActiveForm::end(); ?>
<?php
	$locationId			 = Yii::$app->session->get('location_id');
	$location = Location::findOne(['id' => $locationId]);
	$from_time = (new \DateTime($location->from_time))->format('H:i:s');
	$to_time = (new \DateTime($location->to_time))->format('H:i:s');
?>

<script type="text/javascript">
function refreshCalendar(date) {
$('#calendar').fullCalendar('destroy');
$('#calendar').fullCalendar({
    defaultDate: moment(date, 'DD-MM-YYYY', true).format('YYYY-MM-DD'),
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'agendaWeek,agendaDay'
    },
    slotDuration: '00:15:00',
    titleFormat: 'DD-MMM-YYYY, dddd',
    defaultView: 'agendaWeek',
    minTime: "<?php echo $from_time; ?>",
    maxTime: "<?php echo $to_time; ?>",
    selectConstraint: 'businessHours',
    eventConstraint: 'businessHours',
    businessHours: <?php echo Json::encode($teacherDetails['availableHours']); ?>,
    allowCalEventOverlap: true,
    overlapEventsSeparate: true,
    events: <?php echo Json::encode($teacherDetails['events']); ?>,
    select: function(start, end, allDay) {
        $('#calendar').fullCalendar('removeEvents', 'newEnrolment');
        $('#course-day').val(moment(start).format('dddd'));
        $('#course-fromtime').val(moment(start).format('h:mm A'));
        $('#course-startdate').val(moment(start).format('DD-MM-YYYY'));
		$('#calendar').fullCalendar('renderEvent',
			{
				id : 'newEnrolment',
				start: start,
				end: end,
				allDay: false
			},
			true // make the event "stick"
		);
    },
    selectable: true,
    selectHelper: true,
  });
}

$(document).ready(function() {
$(document).on('change', '#reschedule-date', function() {
	var date = $('#reschedule-date').val();
    refreshCalendar(date);
});
});
</script>