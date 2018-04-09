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
	<div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Program</label>
        </div>
        <div class="col-xs-5 text-right">
            <?=
        $form->field($model, 'programId')->widget(Select2::classname(), [
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
	<div class="clearfix"></div>
	<div class="form-group pull-right">
		<?= Html::a('Cancel', '#', ['class' => 'm-r-10 btn btn-default new-enrol-cancel']); ?>
		<button class="step1-next btn btn-info pull-right" type="button" >Next</button>
    </div>
</div>
<?php
$locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
$minLocationAvailability = LocationAvailability::find()
    ->andWhere(['locationId' => $locationId])
    ->orderBy(['fromTime' => SORT_ASC])
    ->one();
$maxLocationAvailability = LocationAvailability::find()
    ->andWhere(['locationId' => $locationId])
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
