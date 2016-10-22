<?php

use common\models\Program;
use common\models\Enrolment;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php
$privatePrograms = ArrayHelper::map(Program::find()
			->active()
			->where(['type' => Program::TYPE_PRIVATE_PROGRAM])
			->all(), 'id', 'name')
?>
<div class="enrolment-form form-well form-well-smw">
    <div class="row">
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
					'autoclose' => true
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'duration')->widget(TimePicker::classname(),
				[
				'pluginOptions' => [
					'showMeridian' => false,
					'defaultTime' => date('H:i', strtotime('00:30')),
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'programId')->dropDownList(
				ArrayHelper::map(Program::find()
						->active()
						->where(['type' => Program::TYPE_PRIVATE_PROGRAM])
						->all(), 'id', 'name'), ['prompt' => 'Select..']);
			?>
		</div>

	</div>
	<div class="row">
		<div class="col-md-4">
<?= $form->field($model, 'paymentFrequency')->radioList(Enrolment::paymentFrequencies()) ?>
		</div>
	</div>

</div>
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<?php
$locationId		 = Yii::$app->session->get('location_id');
$location		 = Location::findOne(['id' => $locationId]);
$from_time		 = (new \DateTime($location->from_time))->format('H:i:s');
$to_time		 = (new \DateTime($location->to_time))->format('H:i:s');
?>
<script>

    $(document).ready(function () {
        $('#stepwizard_step2_save').click(function () {
            $('#enrolment-form').submit();
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href") // activated tab
            loadCalendar();
        });
    });
    function loadCalendar() {
        $('#calendar').fullCalendar({
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

