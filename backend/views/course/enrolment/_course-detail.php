<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Url;
use kartik\select2\Select2;
?>

<?php
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['course/create-enrolment-detail', 'studentId' => $student ? $student->id : null, 
            'isReverse' => $isReverse, 'EnrolmentForm' => $model])
    ]);
?>
<div class="user-create-form">
    <div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'teacherId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(
                    $teachers,
                    'id',
                    'userProfile.fullName'
                ),
                'options' => ['placeholder' => 'Teacher'],
                'hashVarLoadPosition' => View::POS_READY
            ])->label('Teacher')
        ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'startDate')->textInput(['readOnly' => true])->label('Start Date');?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'day')->textInput(['readOnly' => true])->label('Day');?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'fromTime')->textInput(['readOnly' => true])->label('Start Time');?>
    </div>
	<?= $form->field($model, 'duration')->hiddenInput()->label(false);?>
    </div>
    <div class="row">
    <div class="col-md-12">
        <div id="enrolment-create-calendar"></div>
    </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<script>
    $(document).on('week-view-calendar-select', function(event, params) {
        $('#enrolmentform-startdate').val(moment(params.date, "DD-MM-YYYY h:mm a").format('MMM D, Y')).trigger('change');
        $('#enrolmentform-day').val(moment(params.date, "DD-MM-YYYY h:mm a").format('dddd')).trigger('change');
        $('#enrolmentform-fromtime').val(moment(params.date, "DD-MM-YYYY h:mm a").format('h:mm a')).trigger('change');
        return false;
    });

    $(document).off('click', '.course-detail-back').on('click', '.course-detail-back', function () {
        $('#modal-spinner').show();
        $.ajax({
            url: '<?= Url::to(['course/create-enrolment-date-detail', 'studentId' => !empty($student) ? $student->id : null,
                'isReverse' => $isReverse, 'EnrolmentForm' => $model]) ?>',
            type: 'get',
            dataType: "json",
            data: $('#modal-form').serialize(),
            success: function (response)
            {
                if (response.status)
                {
                    $('.modal-back').hide();
                    $('#modal-content').html(response.data);
                    $('#customer-discount').val(response.customerDiscount);
                    $('#modal-spinner').hide();
                }
            }
        });
        return false;
    });

    $(document).ready(function (event) {
        $('#modal-back').show();
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">New Enrolment Detail</h4>');
        var isReverse = <?= $isReverse ?>;
        $('.modal-save').show();
        if (isReverse == 1) {
            $('.modal-save').text('Next');
        } else {
            $('.modal-save').text('Preview Lessons');
        }
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        var options = {
            'date' : $('#enrolmentform-startdate').val(),
            'renderId' : '#enrolment-create-calendar',
            'eventUrl' : '<?= Url::to(['teacher-availability/show-lesson-event']) ?>',
            'availabilityUrl' : '<?= Url::to(['teacher-availability/availability']) ?>',
            'changeId' : '#enrolmentform-teacherid',
            'durationId' : '#enrolmentform-duration',
            'studentId' : '<?= $student ? $student->id : null ?>'
        };
        $(document).off('change', options.changeId).on('change', options.changeId, function () {
            $.fn.calendarDayView(options);
        });
        $('#modal-spinner').hide();
        $('#modal-back').removeClass();
        $('#modal-back').addClass('btn btn-info course-detail-back');
    });

    $(document).on('week-calendar-after-render', function(event, params) {
        var event = $('#week-view-calendar').fullCalendar('clientEvents', 'newEnrolment');
        var newevent = $('#week-view-calendar').fullCalendar('clientEvents', 3);
        var time = $('#enrolmentform-fromtime').val();
        if ($.isEmptyObject(event) && $.isEmptyObject(newevent) && !$.isEmptyObject(time)) {
            var duration = '<?= $model->duration; ?>';
            var durationMinutes = moment.duration(duration).asMinutes();
            var start = moment($('#enrolmentform-startdate').val() + ' ' + $('#enrolmentform-fromtime').val());
            var endtime = start.clone();
            var end = moment(endtime.add(durationMinutes, 'minutes'));
            $('#week-view-calendar').fullCalendar('renderEvent',
                {
                    id: 3,
                    start: start,
                    end: end,
                    allDay: false
                },
            true // make the event "stick"
            );
            $('#week-view-calendar').fullCalendar('unselect');
        }
    });
</script>
