<?php

use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\User;
use yii\helpers\Url;
use kartik\time\TimePicker;
use common\models\Location;
?>

<div id="bulk-reschedule" style="display: none;" class="alert-danger alert fade in"></div>
<div class="enrolment-form">
    <?php $form = ActiveForm::begin([
        'id' => 'enrolment-update',
        'action' => Url::to(['enrolment/update', 'id' => $model->id])
    ]); ?>
    <div class="row">
        <div class="col-md-3">
            <?php $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
            $teachers = ArrayHelper::map(
                User::find()
                    ->notDeleted()
                    ->teachers($course->programId, $locationId)
                    ->join('LEFT JOIN', 'user_profile', 'user_profile.user_id=ul.user_id')
                    ->orderBy(['user_profile.firstname'=> SORT_ASC])
                    ->all(),
                'id',
                'publicIdentity'
            );
            ?>
            <?php
            echo $form->field($course, 'teacherId')->widget(Select2::classname(), [
                'data' => $teachers,
                'options' => [
                    'id' => 'course-teacherid',
                    'placeholder' => 'Select teacher',
                ]
            ]);
            ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($courseSchedule, 'dayTime')->textInput(['readOnly' => true])->label('Day');?>
        </div>
        <div class="col-md-2">
            <?= $form->field($courseSchedule, 'duration')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => (new \DateTime($courseSchedule->duration))->format('H:i'),
                ]
            ])->label('Duration');?>
        </div>
        <div class="col-md-2">
            <?= $form->field($course, 'startDate')->widget(DatePicker::classname(),
                [
                'options' => [
                    'readOnly' => true,
                    'value' => (new \DateTime())->format('d-m-Y'),
                ],
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]);?>
        </div>
        <div class="col-md-2">
            <?= $form->field($course, 'endDate')->widget(
                DatePicker::classname(),
                [
                'options' => [
                    'readOnly' => true,
                    'value' => (new \DateTime($course->endDate))->format('d-m-Y'),
                ],
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]); ?>
        </div>
        <?= $form->field($courseSchedule, 'day')->hiddenInput()->label(false);?>
        <?= $form->field($courseSchedule, 'fromTime')->hiddenInput()->label(false);?>
        <div class="col-md-12">
            <div id="bulk-reschedule-calendar"></div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var options = {
            'renderId' : '#bulk-reschedule-calendar',
            'eventUrl' : '<?= Url::to(['teacher-availability/show-lesson-event']) ?>',
            'availabilityUrl' : '<?= Url::to(['teacher-availability/availability-with-events']) ?>',
            'changeId' : '#course-teacherid',
            'durationId' : '#courseschedule-duration'
        };
        $.fn.calendarDayView(options);
    });

    $(document).on('week-view-calendar-select', function(event, params) {
        $('#courseschedule-day').val(moment(params.date).format('e')).trigger('change');
        $('#courseschedule-daytime').val(moment(params.date).format('dddd hh:mm A')).trigger('change');
        $('#courseschedule-fromtime').val(moment(params.date).format('HH:MM:SS')).trigger('change');
        return false;
    });

    $(document).on('modal-success', function(event, params) {
        paymentFrequency.onEditableSuccess();
        return false;
    });
</script>