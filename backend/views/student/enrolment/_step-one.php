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
<div class="row user-create-form">
	<div class="col-xs-6 pull-left">
        <label>Program</label>
    </div>
	<div class="col-xs-6">
	<?= $form->field($model, 'programId')->widget(Select2::classname(), [
		'data' => $privatePrograms,
		'options' => ['placeholder' => 'Program']
	])->label(false)
	?>
	</div>
	<div class="col-xs-6 pull-left">
        <label>Rate (per hour)</label>
    </div>
	<div class="col-xs-2"></div>
	<div class="col-xs-4">
	<?php if (Yii::$app->user->identity->isAdmin()) : ?>
		<?php
		echo $form->field($courseSchedule, 'programRate',[
				'inputTemplate' => '<div class="input-group">'
				. '<span class="input-group-addon">$</span>{input}<span class="input-group-addon">/hr</span></div>',]
			)->textInput(['class' => 'col-md-2 form-control	'])
			->label(false);
		?>
	<?php endif; ?>
	</div>
	<div class="col-xs-6 pull-left">
        <label>Duration</label>
    </div>
	<div class="col-xs-2"></div>
	<div class="col-xs-4">
	<?php
	echo $form->field($courseSchedule, 'duration')->widget(TimePicker::classname(), [
		'pluginOptions' => [
			'showMeridian' => false,
			'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
		],
	])->label(false);
	?>
	</div>
	<div class="col-xs-6 pull-left">
        <label>Rate (per month)</label>
    </div>
	<div class="col-xs-2"></div>
	<div class="col-xs-4">
		<div class="form-group">
		<div class="input-group">
			<span class="input-group-addon">$</span>
			<input type="text" readonly="true" id="rate-per-month" class="col-md-2 form-control	" autocomplete="off" >
			<span class="input-group-addon">/mn</span>
		</div>
		</div>
	</div>
	<div class="col-xs-6 pull-left">
        <label>Payment Frequency</label>
    </div>
	<div class="col-xs-6">
	<?=
	$form->field($courseSchedule, 'paymentFrequency')->widget(Select2::classname(), [
		'data' => ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name')
	])->label(false)
	?>
	</div>
	<div class="col-xs-6 pull-left">
        <label>Payment Frequency Discount</label>
    </div>
	<div class="col-xs-2"></div>
	<div class="col-xs-4">
	<?=
	$form->field($paymentFrequencyDiscount, 'discount', [
		'inputTemplate' => '<div class="input-group">'
		. '{input}<span class="input-group-addon">%</span></div>'])->textInput([
		'id' => 'payment-frequency-discount',
		'name' => 'PaymentFrequencyDiscount[discount]'
	])->label(false);
	?>
	</div>
	<div class="col-xs-7 pull-left">
        <label>Multiple Enrol. Discount (per month)</label>
    </div>
	<div class="col-xs-1"></div>
	<div class="col-xs-4">
	<?=
	$form->field($multipleEnrolmentDiscount, 'discount', [
		'inputTemplate' => '<div class="input-group">'
		. '<span class="input-group-addon">$</span>{input}<span class="input-group-addon">/mn</span></div>'])->textInput([
		'id' => 'enrolment-discount',
		'name' => 'MultipleEnrolmentDiscount[discount]'
	])->label(false);
	?>
	</div>
	<div class="col-xs-7 pull-left">
        <label>Discounted Rate (per month)</label>
    </div>
	<div class="col-xs-1"></div>
	<div class="col-xs-4">
		<div class="form-group">
		<div class="input-group">
			<span class="input-group-addon">$</span>
			<input type="text" readonly="true" id="discounted-rate-per-month" class="col-md-2 form-control	" autocomplete="off" >
			<span class="input-group-addon">/mn</span>
		</div>
		</div>
	</div>
<div class="form-group pull-right">
	<button class="btn btn-info pull-right step1-next" type="button" >Next</button>
	<?= Html::a('Cancel', '#', ['class' => 'm-r-10 pull-right btn btn-default private-enrol-cancel']); ?>
</div>
</div>
