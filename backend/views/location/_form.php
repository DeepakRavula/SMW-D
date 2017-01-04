<?php

use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\City;
use common\models\Province;
use common\models\Country;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Location */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div id="myflashwrapper" style="display: none;" class="alert-success alert fade in"></div>

<div class="location-form">

	<?php $form = ActiveForm::begin(); ?>
		<div class="row">
		<div class="col-md-4">
			<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4 ">
			<?php echo $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'phone_number')->textInput() ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'city_id')->dropDownList(ArrayHelper::map(
                            City::find()->all(), 'id', 'name'
            ))
            ?>
		</div>
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'province_id')->dropDownList(ArrayHelper::map(
                            Province::find()->all(), 'id', 'name'
            ))
            ?>
		</div>
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'country_id')->dropDownList(ArrayHelper::map(
                            Country::find()->all(), 'id', 'name'
            ))
            ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="row">
		<div class="col-md-4">
			<?php echo $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">
			<?php
            if (!$model->isNewRecord) {
                $model->from_time = Yii::$app->formatter->asTime($model->from_time);
                $model->to_time = Yii::$app->formatter->asTime($model->to_time);
            }
            ?>
			<?php
            echo $form->field($model, 'from_time')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'showMeridian' => true,
                ],
            ]);
            ?>
		</div>
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'to_time')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'showMeridian' => true,
                ],
            ]);
            ?>
		</div>
	</div>
	<div class="clearfix"></div>

    <div class="form-group">
<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php
            if (!$model->isNewRecord) {
                echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
            }
        ?>
	</div>
<?php ActiveForm::end(); ?>

</div>
<div id="calendar"></div>

<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
        defaultView: 'agendaDay',
        minTime: "00:00:00",
        maxTime: "23:59:59",
        slotDuration: "00:15:00",
        editable: true,
        draggable: false,
        droppable: false,
        resources: [{'id':'0','title':'Sunday'},{'id':'1', 'title':'Monday'}, {'id':'2','title':'Tuesday'},
            {'id':'3','title':'Wednesday'}, {'id':'4','title':'Thursday'}, {'id':'5','title':'Friday'}, {'id':'6','title':'Saturday'}],
        events: <?php echo Json::encode($events); ?>,
        eventResize: function(event) {
            var endTime = moment(event.end).format('YYYY-MM-DD HH:mm:ss');
            var startTime = moment(event.start).format('YYYY-MM-DD HH:mm:ss');
            var params = $.param({ resourceId: event.resourceId, startTime: startTime, endTime: endTime });
            $.ajax({
                url    : '<?= Url::to(['location/edit-availability', 'id' => $model->id]) ?>&' + params,
                type   : 'POST',
                dataType: 'json',
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#myflashwrapper').text("Availability Successfully modified").fadeIn().delay(3000).fadeOut();
                    }
                }
            });

        },
        eventDrop: function(event) {
            var endTime = moment(event.end).format('YYYY-MM-DD HH:mm:ss');
            var startTime = moment(event.start).format('YYYY-MM-DD HH:mm:ss');
            var params = $.param({ resourceId: event.resourceId, startTime: startTime, endTime: endTime });
            $.ajax({
                url    : '<?= Url::to(['location/edit-availability', 'id' => $model->id]) ?>&' + params,
                type   : 'POST',
                dataType: 'json',
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#myflashwrapper').text("Availability Successfully modified").fadeIn().delay(3000).fadeOut();
                    }
                }
            });
        }
    });
});
</script>