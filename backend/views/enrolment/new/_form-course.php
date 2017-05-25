<?php

use common\models\Program;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use common\models\PaymentFrequency;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'New Enrolment';
?>
<div class="container">
		 <div class="form-group">
			<label class="col-sm-2 control-label">Program</label>
			<div class="col-sm-4">
				<?php
            echo $form->field($model, 'programId')->dropDownList(
                ArrayHelper::map(Program::find()
					->active()
					->all(), 'id', 'name'))->label(false);
            ?>
			</div>
		</div>
	<div class="clearfix"></div>
	 <div class="form-group">
		<label  class="col-sm-2 control-label">Length of Lessons</label>
		<div class="col-sm-3">
			<?php
            echo $form->field($model, 'duration')->widget(TimePicker::classname(),
                [
                'options' => ['id' => 'course-duration'],
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
                ],
            ])->label(false);
            ?>
		</div>
		<label  class="col-sm-2 control-label">Check The Schedule</label>
		<div class="col-sm-1  hand enrolment-calendar-icon">
            <span class="fa fa-calendar"></span>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Teacher</label>
    	<?php echo $form->field($model, 'teacherId')->hiddenInput()->label(false) ?>
		<div class="col-sm-5 new-enrolment-teacher">
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Day, Time & Duration</label>
    	<?php echo $form->field($model, 'duration')->hiddenInput()->label(false) ?>
    	<?php echo $form->field($model, 'fromTime')->hiddenInput()->label(false) ?>
		<div class="col-sm-5 new-enrolment-time">
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label">Payment Frequency</label>
		<div class="col-sm-3">
			<?= $form->field($model, 'paymentFrequency')->dropDownList(ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name'))->label(false) ?>	
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label">Discount</label>
		<div class="col-sm-1">
		<?= $form->field($model, 'discount')->textInput()->label(false);?>
		</div>
		<span class="col-sm-1">%</span>
	</div>
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
	function rateEstimation(duration, programRate, discount) {
		var timeArray = duration.split(':');
    	var hours = parseInt(timeArray[0]);
    	var minutes = parseInt(timeArray[1]);
		var unit = ((hours * 60) + (minutes)) / 60;
		var amount = (programRate * unit).toFixed(2);
		if(discount === '') {
			var discount = 0;
		} 
		var discountedRate = (amount - ((amount * (discount / 100)))).toFixed(2);
		var discountedMonthlyRate = (discountedRate * 4).toFixed(2); 
		$('#rate').text('$' + discountedRate);
		$('#monthly-rate').text('$' + discountedMonthlyRate);
	}
	function fetchProgram(duration, programId, discount) {
		$.ajax({
			url: '<?= Url::to(['student/fetch-program-rate']); ?>' + '?id=' + programId,
			type: 'get',
			dataType: "json",
			success: function (response)
			{
				programRate = response;
				rateEstimation(duration,programRate, discount);
			}
		});
	}
    $(document).ready(function () {
		$('#rate').text('$0.00');
		$('#monthly-rate').text('$0.00');
		$(document).on('change', '#course-programid', function(){
			var duration = $('#course-duration').val();
			var programId = $('#course-programid').val();
			var discount = $('#course-discount').val();
			fetchProgram(duration, programId, discount);
		});
		$(document).on('change', '#course-duration', function(){
			var duration = $('#course-duration').val();
			var programId = $('#course-programid').val();
			var discount = $('#course-discount').val();
			if (duration && programId || discount) {
				fetchProgram(duration, programId, discount);
			}
		});
		$(document).on('change', '#course-discount', function(){
			var duration = $('#course-duration').val();
			var programId = $('#course-programid').val();
			var discount = $('#course-discount').val();
			if (duration && programId || discount) {
				fetchProgram(duration, programId, discount);
			}
		});
});
</script>