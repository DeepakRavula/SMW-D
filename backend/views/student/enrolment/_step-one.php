<?php

use kartik\select2\Select2;
use common\models\Program;
use common\models\PaymentFrequency;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use yii\helpers\Html;
use common\models\LocationAvailability;
?>
<?php
$privatePrograms = ArrayHelper::map(Program::find()
			->active()
			->andWhere(['type' => Program::TYPE_PRIVATE_PROGRAM])
			->all(), 'id', 'name')
?>
<div class="user-create-form">
	<div class="row">
		<div class="col-xs-6">
			<label class="modal-form-label">Program</label>
		</div>
		<div class="col-xs-5 text-right">
		<?= $form->field($model, 'programId')->widget(Select2::classname(), [
			'data' => $privatePrograms,
			'options' => ['placeholder' => 'Program']
		])->label(false)
		?>
		</div>
	</div>
	<?php if (Yii::$app->user->identity->isAdmin()) : ?>
	<div class="row">
		<div class="col-xs-6">
			<label class="modal-form-label">Rate (per hour)</label>
		</div>
		<div class="col-xs-2 enrolment-dollar"><label class="text-muted">$</label></div>
		<div class="col-xs-3 enrolment-field">
				<?php
				echo $form->field($courseSchedule, 'programRate')->textInput(['class' => 'form-control	'])
					->label(false);
				?>
		</div>
		<div class="col-xs-1 enrolment-text"><label class="text-muted">/hr</label></div>
	</div>
	<?php endif; ?>
	<div class="row">
		<div class="col-xs-6">
			<label class="modal-form-label">Duration</label>
		</div>
		<div class="col-xs-2"></div>
		<div class="col-xs-3">
		<?php
		echo $form->field($courseSchedule, 'duration')->widget(TimePicker::classname(), [
			'pluginOptions' => [
				'showMeridian' => false,
				'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
			],
		])->label(false);
		?>
		</div>
		<div class="col-xs-1 enrolment-text"><label class="text-muted">mins.</label></div>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<label class="modal-form-label">Rate (per month)</label>
		</div>
		<div class="col-xs-2 enrolment-dollar"><label class="text-muted">$</label></div>
		<div class="col-xs-3">
			<div class="form-group">
			<div class="input-group">
				<input type="text" readonly="true" id="rate-per-month" class="col-md-2 form-control	" autocomplete="off" >
			</div>
			</div>
		</div>
		<div class="col-xs-1 enrolment-text"><label class="text-muted">/mn</label></div>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<label class="modal-form-label">Payment Frequency</label>
		</div>
		<div class="col-xs-5 text-right">
		<?=
		$form->field($courseSchedule, 'paymentFrequency')->widget(Select2::classname(), [
			'data' => ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name')
		])->label(false)
		?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<label class="modal-form-label">Payment Frequency Discount</label>
		</div>
		<div class="col-xs-2"></div>
		<div class="col-xs-3">
			<?=
			$form->field($paymentFrequencyDiscount, 'discount')->textInput([
				'id' => 'payment-frequency-discount',
				'name' => 'PaymentFrequencyDiscount[discount]'
			])->label(false);
			?>
		</div>
		<div class="col-xs-1 enrolment-text"><label class="text-muted">%</label></div>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<label class="modal-form-label">Multiple Enrol. Discount (per month)</label>
		</div>
		<div class="col-xs-2 enrolment-dollar"><label class="text-muted">$</label></div>
		<div class="col-xs-3">
			<?=
			$form->field($multipleEnrolmentDiscount, 'discount')->textInput([
				'id' => 'enrolment-discount',
				'name' => 'MultipleEnrolmentDiscount[discount]'
			])->label(false);
			?>
		</div>
		<div class="col-xs-1 enrolment-text"><label class="text-muted">/mn</label></div>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<label class="modal-form-label">Discounted Rate (per month)</label>
		</div>
		<div class="col-xs-2 enrolment-dollar"><label class="text-muted">$</label></div>
		<div class="col-xs-3">
			<div class="form-group">
			<div class="input-group">
				<input type="text" readonly="true" id="discounted-rate-per-month" class="col-md-2 form-control	" autocomplete="off" >
			</div>
			</div>
		</div>
		<div class="col-xs-1 enrolment-text"><label class="text-muted">/mn</label></div>
	</div>
	<div class="row">
		<div class="form-group pull-right">
			<button class="btn btn-info pull-right step1-next" type="button" >Next</button>
			<?= Html::a('Cancel', '#', ['class' => 'm-r-10 pull-right btn btn-default private-enrol-cancel']); ?>
		</div>
	</div>
</div>
