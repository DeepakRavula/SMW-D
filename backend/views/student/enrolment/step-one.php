<?php

use common\models\Program;
use common\models\PaymentFrequency;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use common\models\LocationAvailability;
use yii\helpers\Url;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php
$privatePrograms = ArrayHelper::map(Program::find()
            ->active()
            ->andWhere(['type' => Program::TYPE_PRIVATE_PROGRAM])
            ->all(), 'id', 'name')
?>
	<div class="row">
		<div class="col-md-3">
			<?php
            echo $form->field($courseSchedule, 'duration')->widget(TimePicker::classname(),
                [
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
                ],
            ]);
            ?>
        </div>
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
                    'autoclose' => true,
                ],
            ]);
            ?>
        </div>
		
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'programId')->widget(Select2::classname(), [
                'data' => $privatePrograms,
                'options' => ['placeholder' => 'Program']
            ]) ?>
        </div>
	</div>
        <div class="col-md-3">
            <?= $form->field($courseSchedule, 'paymentFrequency')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name')
                ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($paymentFrequencyDiscount, 'discount')->textInput([
                'id' => 'payment-frequency-discount',
                'name' => 'PaymentFrequencyDiscount[discount]'
            ])->label('Payment Frequency Discount(%)'); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($multipleEnrolmentDiscount, 'discount')->textInput([
                'id' => 'enrolment-discount',
                'name' => 'MultipleEnrolmentDiscount[discount]'
            ])->label('Multiple Enrol. Discount($) Per month'); ?>
        </div>
	<div class="row" id="course-rate-estimation">
	<div class="col-md-6">	
		<?php
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'title' => 'What\'s that per month?',
			'withBorder' => true,
		])
		?>
		 <p id="before-discount"></p>	
		<?php LteBox::end() ?>
		</div> 
		<div class="col-md-6">	
		<?php
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'title' => 'After Discount',
			'withBorder' => true,
		])
		?>
		<div id="after-discount"></div>
		<?php LteBox::end() ?>
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
    var enrolment = {
	fetchProgram: function(duration, programId, paymentFrequencyDiscount, multiEnrolmentDiscount) {
            var params = $.param({duration: duration, id: programId, paymentFrequencyDiscount: paymentFrequencyDiscount,
                multiEnrolmentDiscount: multiEnrolmentDiscount });
            $.ajax({
                url: '<?= Url::to(['student/fetch-program-rate']); ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    $('#course-rate-estimation').show();
                    $('#before-discount').text(response.beforeDiscount);
                    $('#after-discount').text(response.afterDiscount);
                }
            });
	}
    };
    $(document).ready(function () {
        $('.next-step').removeClass('btn-default');
        $('.next-step').addClass('btn-info');
        $('#course-rate-estimation').hide();
        $(document).on('change', '#course-programid, #courseschedule-duration, #payment-frequency-discount, #enrolment-discount', function(){
            var duration = $('#courseschedule-duration').val();
            var programId = $('#course-programid').val();
            var paymentFrequencyDiscount = $('#payment-frequency-discount').val();
            var multiEnrolmentDiscount = $('#enrolment-discount').val();
            enrolment.fetchProgram(duration, programId, paymentFrequencyDiscount, multiEnrolmentDiscount);
        });
        $('#stepwizard_step1_next').click(function() {
                var $active = $('.wizard .nav-tabs li.active');
                $active.removeClass('active').addClass('disabled');
                $('#enrolment-form').data('yiiActiveForm').submitting = true;
                $('#enrolment-form').yiiActiveForm('remove', 'courseschedule-day');
                $('#enrolment-form').yiiActiveForm('remove', 'courseschedule-fromtime');
                $('#enrolment-form').yiiActiveForm('validate');
                $('#notification').remove();
        });
        $('#enrolment-form').on('afterValidate', function (event, messages) {
			if(messages["course-programid"].length || messages["courseschedule-paymentfrequency"].length) {
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
            var courseDay = $('#courseschedule-day').val();
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

