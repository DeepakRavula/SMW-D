<?php

use common\models\Program;
use common\models\Enrolment;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use common\models\Location;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php
$privatePrograms = ArrayHelper::map(Program::find()
			->active()
			->where(['type' => Program::TYPE_PRIVATE_PROGRAM])
			->all(), 'id', 'name')
?>
<div class="enrolment-form form-well form-well-smw">
    <div class="row">
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'startDate')->widget(DatePicker::classname(),
				[
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
				'options' => [
					'value' => (new \DateTime())->format('d-m-Y'),
				],
				'pluginOptions' => [
					'format' => 'dd-mm-yyyy',
					'todayHighlight' => true,
					'autoclose' => true
				]
			]);
			?>
			<?php
			echo $form->field($model, 'duration')->widget(TimePicker::classname(),
				[
				'pluginOptions' => [
					'showMeridian' => false,
					'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
				]
			]);
			?>
			<?php
			echo $form->field($model, 'programId')->dropDownList(
				ArrayHelper::map(Program::find()
						->active()
						->where(['type' => Program::TYPE_PRIVATE_PROGRAM])
						->all(), 'id', 'name'), ['prompt' => 'Select..']);
			?>
			
<?= $form->field($model, 'paymentFrequency')->radioList(Enrolment::paymentFrequencies()) ?>
	</div>
		<div id="course-rate-estimation" class="col-md-4">
			<p class="text-info">
			<strong>What's that per month?</strong></p>
			<div class="smw-box col-md-12 m-l-20 m-b-30 course-monthly-estimate">
				<div>
			Four <span id="duration"></span>min Lessons @ $<span id="rate-30-min"></span> each = $<span id="rate-month-30-min"></span>/mn
				</div>
			</div>
		</div>
	</div>
</div>
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<?php
$locationId		 = Yii::$app->session->get('location_id');
$location		 = Location::findOne(['id' => $locationId]);
$from_time		 = (new \DateTime($location->from_time))->format('H:i:s');
$to_time		 = (new \DateTime($location->to_time))->format('H:i:s');
?>
<script>
	function rateEstimation(duration, programRate) {
		var timeArray = duration.split(':');
    	var hours = parseInt(timeArray[0]);
    	var minutes = parseInt(timeArray[1]);
		var unit = ((hours * 60) + (minutes)) / 60;
		var duration = (hours * 60) + minutes;
		$('#duration').text(duration);
		var amount = (programRate * unit).toFixed(2);
		$('#rate-30-min').text(amount);
		var ratePerMonth30 = ((amount) * 4).toFixed(2);
		$('#rate-month-30-min').text(ratePerMonth30);
		$('#course-rate-estimation').show();
	}
	function fetchProgram(duration, programId) {
		$.ajax({
			url: '<?= Url::to(["student/fetch-program-rate"]); ?>' + '?id=' + programId,
			type: 'get',
			dataType: "json",
			success: function (response)
			{
				programRate = response;
				rateEstimation(duration,programRate);
			}
		});
	}
    $(document).ready(function () {
		$('#course-rate-estimation').hide();
		$(document).on('change', '#course-programid', function(){
			var duration = $('#course-duration').val();
			var programId = $('#course-programid').val();
			fetchProgram(duration, programId);
		});
		$(document).on('change', '#course-duration', function(){
			var duration = $('#course-duration').val();
			var programId = $('#course-programid').val();
			if (duration && programId) {
				fetchProgram(duration, programId);
			}
		});
		$('#stepwizard_step1_next').unbind().click(function() {
			
			$('#enrolment-form').yiiActiveForm('validateAttribute', 'course-programid');
			$('#enrolment-form').yiiActiveForm('validateAttribute', 'course-paymentfrequency');
			$('#enrolment-form').yiiActiveForm('validateAttribute', 'course-startdate');
        		if(!  isStep1valid ) {
					var $active = $('.wizard .nav-tabs li.active');
					$active.removeClass('active').addClass('disabled');
				} 
		});
		var isStep1valid;
        $('#enrolment-form').on('afterValidateAttribute', function (event, attribute, messages) {
			//console.log(event);
			console.log(attribute.status);
			console.log(messages.length);
           if (attribute.name === 'programId' && attribute.name === 'paymentFrequency') {
                if( messages.length > 0) {
                isStep1valid = false;
            }  else{
			   console.log('come');
                isStep1valid = true;
            }
		}
        });
        $('#stepwizard_step2_save').click(function () {
            $('#enrolment-form').submit();
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href") // activated tab
            loadCalendar();
        });
    });
    function loadCalendar() {
        $('#calendar').fullCalendar({
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
            businessHours: [],
            allowCalEventOverlap: true,
            overlapEventsSeparate: true,
            events: [],
        });
    }
</script>

