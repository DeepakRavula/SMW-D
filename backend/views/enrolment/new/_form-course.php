<?php

use common\models\Program;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use common\models\PaymentFrequency;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\User;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'New Enrolment';
?>
<div class="row">
		 <div class="form-group">
			<label class="col-sm-2 control-label">Program</label>
			<div class="col-sm-4">
				<?php
            echo $form->field($model, 'programId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Program::find()
					->active()
					->privateProgram()
					->all(), 'id', 'name'),
                'options' => ['placeholder' => 'Program']
            ])->label(false);
            ?>
			</div>
		</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Teacher</label>
		<?php $locationId = Yii::$app->session->get('location_id');
        $teachers = ArrayHelper::map(
			User::find()
				->notDeleted()
				->teachers($model->programId, $locationId)
				->all(), 'id', 'publicIdentity');
        ?>
		<div class="col-sm-4">
        <?php
        // Dependent Dropdown
        echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
                'data' => $teachers,
                'type' => DepDrop::TYPE_SELECT2,
                'options' => [
                    'placeholder' => 'Teacher',
                ],
                'pluginOptions' => [
                    'depends' => ['course-programid'],
                    'url' => Url::to(['/course/teachers'])
                ]
            ])->label(false);
        ?>
		</div>
	</div>
	<div class="clearfix"></div>
	 <div class="form-group">
		<label  class="col-sm-2 control-label">Length of Lessons</label>
		<div class="col-sm-3">
			<?php
            echo $form->field($courseSchedule, 'duration')->widget(TimePicker::classname(),
                [
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
                ],
            ])->label(false);
            ?>
		</div>
		<label  class="col-sm-2 control-label">Check The Schedule</label>
		<div class="col-sm-1  hand enrolment-calendar-icon">
            <span class="fa fa-calendar" style="font-size:30px; margin:-12px 32px;"></span>
		</div>
	</div>
	<div class="clearfix"></div>
	
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Day, Time & Duration</label>
    	<?php echo $form->field($model, 'startDate')->hiddenInput()->label(false) ?>
    	<?php echo $form->field($courseSchedule, 'day')->hiddenInput()->label(false) ?>
    	<?php echo $form->field($courseSchedule, 'fromTime')->hiddenInput()->label(false) ?>
		<div class="col-sm-5 new-enrolment-time">
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-3 control-label">Payment Frequency</label>
		<div class="col-sm-2">
			<?= $form->field($courseSchedule, 'paymentFrequency')->dropDownList(ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name'))->label(false) ?>	
		</div>
	</div>
	<div class="form-group">
		<label  class="col-sm-4 control-label">Payment Frequency Discount (%)</label>
		<div class="col-sm-2">
			<?= $form->field($paymentFrequencyDiscount, 'discount')->textInput([
                'id' => 'payment-frequency-discount',
                'name' => 'PaymentFrequencyDiscount[discount]'
            ])->label(false); ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label">Multiple Enrolment Discount - Per Month($)</label>
		<div class="col-sm-3">
			<?= $form->field($multipleEnrolmentDiscount, 'discount')->textInput([
                'id' => 'enrolment-discount',
                'name' => 'MultipleEnrolmentDiscount[discount]'
            ])->label(false); ?>	
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Rate Per Lesson</label>
		<div class="col-sm-5" id="rate"></div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Rate Per Month</label>
		<div class="col-sm-5" id="monthly-rate"></div>
	</div>
	<div class="clearfix"></div>
</div> <!-- ./container -->
<script>
	function rateEstimation(duration, programRate, pfDiscount, enrolmentDiscount) {
		var timeArray = duration.split(':');
    	var hours = parseInt(timeArray[0]);
    	var minutes = parseInt(timeArray[1]);
		var unit = ((hours * 60) + (minutes)) / 60;
		var amount = (programRate * unit).toFixed(2);
		if(pfDiscount === '') {
			var pfDiscount = 0;
		} 
		var pfDiscountedAmount = amount * (pfDiscount / 100); 
		var enrolmentDiscountPerLesson = enrolmentDiscount / 4;
		var totalDiscountPerLesson = pfDiscountedAmount + enrolmentDiscountPerLesson; 
		var discountedRate = (amount - totalDiscountPerLesson).toFixed(2);
		var discountedMonthlyRate = (discountedRate * 4).toFixed(2); 
		$('#rate').text('$' + discountedRate);
		$('#monthly-rate').text('$' + discountedMonthlyRate);
	}
	function fetchProgram(duration, programId, pfDiscount, enrolmentDiscount) {
		$.ajax({
			url: '<?= Url::to(['student/fetch-program-rate']); ?>' + '?id=' + programId,
			type: 'get',
			dataType: "json",
			success: function (response)
			{
				programRate = response;
				rateEstimation(duration,programRate, pfDiscount, enrolmentDiscount);
			}
		});
	}
    $(document).ready(function () {
		$('#rate').text('$0.00');
		$('#monthly-rate').text('$0.00');
		$(document).on('change', '#course-programid', function(){
			var duration = $('#courseschedule-duration').val();
			var programId = $('#course-programid').val();
			var pfDiscount = $('#payment-frequency-discount').val();
			var enrolmentDiscount = $('#enrolment-discount').val();
			fetchProgram(duration, programId, pfDiscount, enrolmentDiscount);
		});
		$(document).on('change', '#courseschedule-duration, #enrolment-discount, #payment-frequency-discount', function(){
			var duration = $('#courseschedule-duration').val();
			var programId = $('#course-programid').val();
			var pfDiscount = $('#payment-frequency-discount').val();
			var enrolmentDiscount = $('#enrolment-discount').val();
			if (duration && programId || pfDiscount || enrolmentDiscount) {
				fetchProgram(duration, programId, pfDiscount, enrolmentDiscount);
			}
		});
	});
</script>