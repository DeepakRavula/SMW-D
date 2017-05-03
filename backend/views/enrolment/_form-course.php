<?php

use common\models\Program;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use common\models\PaymentFrequency;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'New Enrolment';
?>
<div class="wizard">
    <ul class="steps">
        <li class="active">Program<span class="chevron"></span></li>
        <li>Customer<span class="chevron"></span></li>
        <li>Student<span class="chevron"></span></li>
        <li>Preview<span class="chevron"></span></li>
    </ul>
</div>
<div class="container">
	<?php $form = ActiveForm::begin(); ?>
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
		<div class="col-sm-3">
			<?php
            echo $form->field($model, 'startDate')->widget(DatePicker::classname(),
                [
                'type' => DatePicker::TYPE_BUTTON,
                'options' => [
                    'value' => (new \DateTime())->format('d-m-Y'),
                ],
                'pluginOptions' => [
                    'format' => 'dd-mm-yyyy',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ])->label(false);
            ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Teacher</label>
		<div class="col-sm-5">
		Teacher Name	
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Day, Time & Duration</label>
		<div class="col-sm-5">
		Tuesdays @ 5.00pm	
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
		<div class="col-sm-5">
			
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Rate Per Month</label>
		<div class="col-sm-5">
			
		</div>
	</div>
	<div class="clearfix"></div>
		<?php ActiveForm::end(); ?>
</div> <!-- ./container -->
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
$(document).ready(function(){
	var duration = $('#course-duration').val();
	var programId = $('#course-programid').val();	
});
</script>