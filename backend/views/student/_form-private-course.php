<?php

use common\models\Program;
use common\models\Enrolment;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="enrolment-form form-well form-well-smw">
	<?php $form = ActiveForm::begin(); ?>
    <div class="row">
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'duration')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'showMeridian' => false,
					'defaultTime' => date('H:i', strtotime('00:30')),
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'programId')->dropDownList(
					ArrayHelper::map(Program::find()
						->active()
						->where(['type' => Program::TYPE_PRIVATE_PROGRAM])
						->all(), 
					'id', 'name'), ['prompt' => 'Select..']); ?>
		</div>
		<div class="col-md-4">
			<?php
			// Dependent Dropdown
			echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
				'options' => ['id' => 'course-teacherid'],
				'pluginOptions' => [
					'depends' => ['course-programid'],
					'placeholder' => 'Select...',
					'url' => Url::to(['/course/teachers']),
				]
			]);
			?>
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
		'model' =>  $model,
    ]) ?>
	</div>
	<div class="row">
		<div class="col-md-4">
        	<?= $form->field($model, 'paymentFrequency')->radioList(Enrolment::paymentFrequencies())?>
		</div>
	</div>
    <div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
<div class="clearfix"></div>
	<?php ActiveForm::end(); ?>

</div>
<?php
	$locationId			 = Yii::$app->session->get('location_id');
	$location = Location::findOne(['id' => $locationId]);
	$from_time = (new \DateTime($location->from_time))->format('H:i:s');
	$to_time = (new \DateTime($location->to_time))->format('H:i:s');
?>
<script type="text/javascript">
function refreshCalendar(availableHours, events) {
$('#calendar').fullCalendar('destroy');
$('#calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'agendaWeek'
    },
	allDaySlot:false,
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
$(document).on('change', '#course-teacherid', function() {
	var events, availableHours;
	var teacherId = $('#course-teacherid').val();
    $.ajax({
        url    : '/teacher-availability/availability-with-events?id=' + teacherId,
        type   : 'get',
        dataType: "json",
        success: function(response)
        {
			events = response.events;
			availableHours = response.availableHours;	
			refreshCalendar(availableHours, events);
        }
        });
});
});
</script>

