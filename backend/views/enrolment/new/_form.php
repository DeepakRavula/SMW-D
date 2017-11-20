<?php

use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Program;
use common\models\PaymentFrequency;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use yii\helpers\Html;
use common\models\LocationAvailability;
use kartik\depdrop\DepDrop;
?>
<div class="row user-create-form">
	<?php
	$privatePrograms = ArrayHelper::map(Program::find()
				->active()
				->andWhere(['type' => Program::TYPE_PRIVATE_PROGRAM])
				->all(), 'id', 'name')
	?>
	<div class="col-md-6">
		<?=
		$form->field($model, 'programId')->widget(Select2::classname(), [
			'data' => $privatePrograms,
			'options' => ['placeholder' => 'Program']
		])
		?>
	</div>
	<div class="col-md-6">
		<?php if (Yii::$app->user->identity->isAdmin()) : ?>
			<?php
			echo $form->field($courseSchedule, 'programRate', [
					'inputTemplate' => '<div class="input-group">'
					. '<span class="input-group-addon">$</span>{input}<span class="input-group-addon">/hr</span></div>',]
				)->textInput(['class' => 'col-md-2 form-control	'])
				->label('Rate(per hour)');
			?>
<?php endif; ?>
	</div>
	<div class="col-md-6">
		<?php
		echo $form->field($courseSchedule, 'duration')->widget(TimePicker::classname(), [
			'pluginOptions' => [
				'showMeridian' => false,
				'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
			],
		]);
		?>
	</div>
	<div class="col-md-6">
		<?php
		// Dependent Dropdown
		echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
			'type' => DepDrop::TYPE_SELECT2,
			'options' => ['id' => 'course-teacherid'],
			'pluginOptions' => [
				'depends' => ['course-programid'],
				'placeholder' => 'Select...',
				'url' => Url::to(['course/teachers']),
			],
		]);
		?>
	</div>
	<div class="col-md-6">
		<?php
		echo $form->field($model, 'startDate', [
			'inputTemplate' => '<div class="input-group">'
			. '{input}<span class="input-group-addon"><i class="fa fa-calendar private-enrol-picker"></i></span></div>',])->textInput(['readOnly' => true])->label('Date & Time');
		?>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label class="control-label">Rate(per month)</label>
			<div class="input-group">
				<span class="input-group-addon">$</span>
				<input type="text" readonly="true" id="rate-per-month" class="form-control"  autocomplete="off">
				<span class="input-group-addon">/mn</span>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<?=
		$form->field($courseSchedule, 'paymentFrequency')->widget(Select2::classname(), [
			'data' => ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name')
		])
		?>
	</div>
	<div class="col-md-6">
		<?=
		$form->field($paymentFrequencyDiscount, 'discount', [
			'inputTemplate' => '<div class="input-group">'
			. '{input}<span class="input-group-addon">%</span></div>'])->textInput([
			'id' => 'payment-frequency-discount',
			'name' => 'PaymentFrequencyDiscount[discount]'
		])->label('Payment Frequency Discount');
		?>
	</div>
	<div class="col-md-6">
		<?=
		$form->field($multipleEnrolmentDiscount, 'discount', [
			'inputTemplate' => '<div class="input-group">'
			. '<span class="input-group-addon">$</span>{input}<span class="input-group-addon">/mn</span></div>'])->textInput([
			'id' => 'enrolment-discount',
			'name' => 'MultipleEnrolmentDiscount[discount]'
		])->label('Multiple Enrol. Discount');
		?>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label class="control-label">Discounted Rate</label>
			<div class="input-group">
				<span class="input-group-addon">$</span>
				<input type="text" readonly="true" id="discounted-rate-per-month" class="form-control"  autocomplete="off">
				<span class="input-group-addon">/mn</span>
			</div>
		</div>
	</div>
	<?= $form->field($courseSchedule, 'day')->hiddenInput()->label(false); ?>
	<?= $form->field($courseSchedule, 'fromTime')->hiddenInput()->label(false); ?>
	<div class="form-group pull-right">
		<?= Html::a('Cancel', '#', ['class' => 'm-r-10 btn btn-default new-enrol-cancel']); ?>
		<button class="nextBtn btn btn-info pull-right" type="button" >Next</button>
    </div>
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
<script>
	var enrolment = {
		fetchProgram: function (duration, programId, paymentFrequencyDiscount, multiEnrolmentDiscount, programRate) {
			var params = $.param({duration: duration, id: programId, paymentFrequencyDiscount: paymentFrequencyDiscount,
				multiEnrolmentDiscount: multiEnrolmentDiscount, rate: programRate});
			$.ajax({
				url: '<?= Url::to(['student/fetch-program-rate']); ?>?' + params,
				type: 'get',
				dataType: "json",
				success: function (response)
				{
					$('#courseschedule-programrate').val(response.rate);
					$('#rate-per-month').val(response.ratePerMonth);
					$('#discounted-rate-per-month').val(response.ratePerMonthWithDiscount);
				}
			});
		}
	};
	$(document).ready(function () {
		$(document).on('change', '#course-programid, #courseschedule-duration, #courseschedule-programrate, #payment-frequency-discount, #enrolment-discount', function () {
			if ($(this).attr('id') != "course-programid") {
				var programRate = $('#courseschedule-programrate').val();
			} else {
				var programRate = null;
			}
			var duration = $('#courseschedule-duration').val();
			var programId = $('#course-programid').val();
			var paymentFrequencyDiscount = $('#payment-frequency-discount').val();
			var multiEnrolmentDiscount = $('#enrolment-discount').val();
			enrolment.fetchProgram(duration, programId, paymentFrequencyDiscount, multiEnrolmentDiscount, programRate);
		});
	});
</script>
