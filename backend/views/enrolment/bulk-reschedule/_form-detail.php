<?php

use yii\bootstrap\ActiveForm;
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
        'id' => 'modal-form',
        'action' => Url::to(['enrolment/reschedule', 'id' => $model->id])
    ]); ?>
    <div class="row">
        <div class="col-md-4">
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
            echo $form->field($courseReschedule, 'teacherId')->widget(Select2::classname(), [
                'data' => $teachers,
                'options' => [
                    'placeholder' => 'Select teacher',
                ]
            ]);
            ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($courseReschedule, 'dayTime')->textInput(['readOnly' => true]);?>
        </div>
        <div class="col-md-2">
            <?= $form->field($courseReschedule, 'duration')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => (new \DateTime($courseSchedule->duration))->format('H:i'),
                ]
            ])->label('Duration');?>
        </div>
        <div class="col-md-2">
            <?= $form->field($courseReschedule, 'rescheduleBeginDate')->textInput(['readOnly' => true]);?>
        </div>
        <div class="col-md-12">
            <div id="bulk-reschedule-calendar"></div>
        </div>
    </div>
    <?= $form->field($courseReschedule, 'dateToChangeSchedule')->hiddenInput()->label(false);?>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $(document).on('click', '.modal-back', function () {
        $('#modal-spinner').show();
        $.ajax({
            url: '<?= Url::to(['enrolment/update', 'id' => $model->id]) ?>',
            type: 'get',
            dataType: "json",
            data: $('#modal-form').serialize(),
            success: function (response)
            {
                if (response.status)
                {
                    $('.modal-back').hide();
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('.modal-save').show();
                    $('.modal-save').text('Next');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Enrolment Edit</h4>');
                    $('#popup-modal .modal-dialog').css({'width': '500px'});
                    $('#modal-spinner').hide();
                }
            }
        });
        return false;
    });
    
    $(document).ready(function() {
        var selectedDate = $('#coursereschedule-reschedulebegindate').val();
        if ($.isEmptyObject(selectedDate)) {
            selectedDate = $('#coursereschedule-datetochangeschedule').val();
        }
        let isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
        let isSafari = navigator.userAgent.toLowerCase().indexOf('safari') > -1;
        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        let formattedDate = selectedDate;
        console.log(isFirefox, isSafari)
        if (isFirefox) {
            formattedDate = selectedDate.split(" ");
            formattedDate = formattedDate[1] + '-' + (Number(months.indexOf(formattedDate[0])) + 1)
        } else if (isSafari) {
            formattedDate = selectedDate.split(" ");
            formattedDate = new Date(formattedDate[1], (Number(months.indexOf(formattedDate[0])) + 1), 0) 
        }
        
       
        var options = {
            'date' : formattedDate,
            'renderId' : '#bulk-reschedule-calendar',
            'eventUrl' : '<?= Url::to(['teacher-availability/show-lesson-event']) ?>',
            'availabilityUrl' : '<?= Url::to(['teacher-availability/availability']) ?>',
            'changeId' : '#coursereschedule-teacherid',
            'durationId' : '#coursereschedule-duration',
            'studentId' : <?= $model->studentId ?>,
            'enrolmentId' : <?= $model->id ?>
        };
        $.fn.calendarDayView(options);
        $('#modal-spinner').hide();
    });

    $(document).off('change', '#coursereschedule-teacherid, #coursereschedule-duration').
        on('change', '#coursereschedule-teacherid, #coursereschedule-duration', function () {
            $('#coursereschedule-daytime').val('');
            $('#week-view-calendar').fullCalendar('removeEvents', 'newEnrolment');
    });

    $(document).on('week-view-calendar-select', function(event, params) {
        $('#coursereschedule-daytime').val(moment(params.date, "DD-MM-YYYY h:mm a").format('dddd hh:mm A')).trigger('change');
        $('#coursereschedule-reschedulebegindate').val(moment(params.date, "DD-MM-YYYY h:mm a").format('MMM D, Y')).trigger('change');
        return false;
    });
</script>
