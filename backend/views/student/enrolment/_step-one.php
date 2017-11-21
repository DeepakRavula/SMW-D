<?php

use kartik\select2\Select2;
use common\models\Program;
use common\models\PaymentFrequency;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use yii\helpers\Html;
use common\models\LocationAvailability;
?>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<?php
$privatePrograms = ArrayHelper::map(Program::find()
			->active()
			->andWhere(['type' => Program::TYPE_PRIVATE_PROGRAM])
			->all(), 'id', 'name')
?>
<?php
echo $form->field($model, 'programId', ['horizontalCssClasses' => [
		'label' => 'col-md-6 control-label',
		'wrapper' => 'col-md-5',
]])->widget(Select2::classname(), [
	'data' => $privatePrograms,
	'options' => ['placeholder' => 'Program']
])
?>

<?php if (Yii::$app->user->identity->isAdmin()) : ?>
	<?php
	echo $form->field($courseSchedule, 'programRate', [
			'horizontalCssClasses' => [
				'label' => 'col-md-6 control-label',
				'wrapper' => 'col-md-5',
			],
			'inputTemplate' => '<div class="input-group">'
			. '<span class="input-group-addon">$</span>{input}<span class="input-group-addon">/hr</span></div>',]
		)->textInput(['class' => 'col-md-2 form-control	'])
		->label('Rate(per hour)');
	?>
<?php endif; ?>
<?php
echo $form->field($courseSchedule, 'duration', ['horizontalCssClasses' => [
		'label' => 'col-md-6 control-label',
		'wrapper' => 'col-md-5',
]])->widget(TimePicker::classname(), [
	'pluginOptions' => [
		'showMeridian' => false,
		'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
	],
]);
?>
<div class="form-group">
	<label class="control-label col-md-6 control-label">Rate(per month)</label>
	<div class="col-md-5">
		<div class="input-group">
			<span class="input-group-addon">$</span>
			<input type="text" readonly="true" id="rate-per-month" class="form-control"  autocomplete="off">
			<span class="input-group-addon">/mn</span>
		</div>
	</div>
</div>
<?=
$form->field($courseSchedule, 'paymentFrequency', ['horizontalCssClasses' => [
		'label' => 'col-md-6 control-label',
		'wrapper' => 'col-md-5',
]])->widget(Select2::classname(), [
	'data' => ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name')
])
?>
<?=
$form->field($paymentFrequencyDiscount, 'discount', [
	'horizontalCssClasses' => [
		'label' => 'col-md-6 control-label',
		'wrapper' => 'col-md-5',
	],
	'inputTemplate' => '<div class="input-group">'
	. '{input}<span class="input-group-addon">%</span></div>'])->textInput([
	'id' => 'payment-frequency-discount',
	'name' => 'PaymentFrequencyDiscount[discount]'
])->label('Payment Frequency Discount');
?>
<?=
$form->field($multipleEnrolmentDiscount, 'discount', [
	'horizontalCssClasses' => [
		'label' => 'col-md-6 control-label',
		'wrapper' => 'col-md-5',
	],
	'inputTemplate' => '<div class="input-group">'
	. '<span class="input-group-addon">$</span>{input}<span class="input-group-addon">/mn</span></div>'])->textInput([
	'id' => 'enrolment-discount',
	'name' => 'MultipleEnrolmentDiscount[discount]'
])->label('Multiple Enrol. Discount');
?>
<div class="form-group">
	<label class="control-label col-md-6 control-label">Discounted Rate</label>
	<div class="col-md-5">
		<div class="input-group">
			<span class="input-group-addon">$</span>
			<input type="text" readonly="true" id="discounted-rate-per-month" class="form-control"  autocomplete="off">
			<span class="input-group-addon">/mn</span>
		</div>
	</div>
</div>
<div class="col-md-12">
	<button class="btn btn-info pull-right nextBtn" type="button" >Next</button>
	<?= Html::a('Cancel', '#', ['class' => 'm-r-10 pull-right btn btn-default private-enrol-cancel']); ?>
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
	$('#enrolment-calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
   		defaultDate: moment(new Date()).format('YYYY-MM-DD'),
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
</script>