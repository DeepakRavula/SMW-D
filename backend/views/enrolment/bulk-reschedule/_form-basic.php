<?php

use kartik\daterange\DateRangePicker;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
?>

<div id="bulk-reschedule" style="display: none;" class="alert-danger alert fade in"></div>
<div class="enrolment-form">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['enrolment/update', 'id' => $model->id])
    ]); ?>
    <div class="user-create-form">
        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($courseReschedule, 'dateToChangeSchedule')->widget(DatePicker::classname(), [
                'options' => [
                    'class' => 'form-control',
                ],
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                    'defaultDate' => (new \DateTime($courseReschedule->dateToChangeSchedule))->format('M d, Y'),
                    'changeMonth' => true,
                    'yearRange' => '1500:3000',
                    'changeYear' => true,
                ]
            ])->label('Date To Change Schedule') ?>
            </div>
        </div>
    </div>
    <?= $form->field($courseReschedule, 'teacherId')->hiddenInput()->label(false);?>
    <?= $form->field($courseReschedule, 'duration')->hiddenInput()->label(false);?>
    <?= $form->field($courseReschedule, 'dayTime')->hiddenInput()->label(false);?>
    <?= $form->field($courseReschedule, 'rescheduleBeginDate')->hiddenInput()->label(false);?>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).on('modal-next', function(event, params) {
        $('.modal-save').show();
        $('.modal-save').text('Preview lessons');
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
    });
    $(document).off('change', '#coursereschedule-datetochangeschedule').on('change', '#coursereschedule-datetochangeschedule', function() {
        var changeDate = $('#coursereschedule-datetochangeschedule').val();
        var changeDate =  moment(changeDate).format('YYYY-MM-DD');
        var currentDate = moment().format('YYYY-MM-DD');
        if (changeDate < currentDate) {
            bootbox.alert("Warning: You've chosen a date in the past. Are you sure you want to continue? (Note: Invoiced lessons will not be affected.)");
        }
    });
</script>
