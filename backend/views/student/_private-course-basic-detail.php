<?php

use common\models\Program;
use common\models\PaymentFrequency;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use common\models\LocationAvailability;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
    #course-paymentfrequency .radio{
        display: inline;
        padding-right: 45px;
    }
    #stepwizard_step1_next{
        /*margin-right:15px;*/
    }  
    .field-course-teacherid{
        margin-top:15px;
        margin-left: -5px;
    }
    #stepwizard_step2_prev{
        margin-top:15px;
    }
    #stepwizard_step2_save{
        margin-right: 15px;
        margin-top:15px;
    }
#stepwizard .tab-content .tab-pane ul.list-inline{
        float: none !important;
}
#stepwizard .tab-content .tab-pane ul.list-inline{
        width: 100%;
        text-align: center;
}
</style>
<?php
$privatePrograms = ArrayHelper::map(Program::find()
            ->active()
            ->where(['type' => Program::TYPE_PRIVATE_PROGRAM])
            ->all(), 'id', 'name')
?>
<div class="enrolment-form form-well form-well-smw ">
    <div class="row">
		<div class="col-md-4 asd">
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
                    'autoclose' => true,
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
			<?php
            echo $form->field($model, 'duration')->widget(TimePicker::classname(),
                [
                'options' => ['id' => 'course-duration'],
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
			<?php
            echo $form->field($model, 'programId')->dropDownList(
                ArrayHelper::map(Program::find()
                        ->where(['type' => Program::TYPE_PRIVATE_PROGRAM])
                        ->active()
                        ->all(), 'id', 'name'), ['prompt' => 'Select..']);
            ?>
        </div>
        <div class="clear-fix"></div>
        <div class="col-md-4">
            <?= $form->field($model, 'paymentFrequency')->dropdownList(ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name')) ?>
	    </div>
		<div class="col-md-2">
            <?= $form->field($model, 'discount')->textInput() ?>
	    </div>
		<div class="col-md-1 p-20">%</div>
		<div class="clearfix"></div>
        <div id="course-rate-estimation">
        	<hr class="default-hr">
				<p class="text-info text-center">
				<strong>What's that per month?</strong></p>
            	<div class="col-md-4"></div>
			<div class="smw-box col-md-offset-4 col-md-4 m-l-0 m-b-30 course-monthly-estimate text-center">
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
			url: '<?= Url::to(['student/fetch-program-rate']); ?>' + '?id=' + programId,
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
		$('#stepwizard_step1_next').click(function() {
			var $active = $('.wizard .nav-tabs li.active');
			$active.removeClass('active').addClass('disabled');
			$('#enrolment-form').data('yiiActiveForm').submitting = true;
			$('#enrolment-form').yiiActiveForm('remove', 'course-day');
			$('#enrolment-form').yiiActiveForm('remove', 'course-fromtime');
			$('#enrolment-form').yiiActiveForm('validate');
			$('#notification').remove();
		});
        $('#enrolment-form').on('afterValidate', function (event, messages) {
			if(messages["course-programid"].length || messages["course-paymentfrequency"].length) {
			}  else{
				var $active = $('.wizard .nav-tabs li:first');
				$active.removeClass('disabled');
			   	$active.next().removeClass('disabled');
			   	nextTab($active);
			   	$('#notification').remove();
			}
        });
        $('#stepwizard_step2_save').click(function () {
            $('#enrolment-form').submit();
        });
		$('#enrolment-form').on('beforeSubmit', function (e) {
            var courseDay = $('#course-day').val();
            if( ! courseDay) {
            	$('#error-notification').html("Please choose a day in the calendar").fadeIn().delay(3000).fadeOut();
				 $(window).scrollTop(0);
				return false;
            }
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href") // activated tab
            loadCalendar();
        });
    });
    function loadCalendar() {
		var date = $('#course-startdate').val();
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
            businessHours: [],
            allowCalEventOverlap: true,
            overlapEventsSeparate: true,
            events: [],
        });
    }
</script>

