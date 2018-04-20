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
        'action' => Url::to(['student/enrolment', 'id' => $student->id])
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
    </div>
    <div class="row">
    <div class="col-md-12">
        <div id="enrolment-create-calendar"></div>
    </div>
    </div>
</div>
<?= $form->field($model, 'duration')->hiddenInput()->label(false);?>
<?= $form->field($model, 'programId')->hiddenInput()->label(false);?>
<?= $form->field($model, 'paymentFrequency')->hiddenInput()->label(false);?>
<?= $form->field($model, 'enrolmentDiscount')->hiddenInput()->label(false);?>
<?= $form->field($model, 'pfDiscount')->hiddenInput()->label(false);?>
<?= $form->field($model, 'programRate')->hiddenInput()->label(false);?>
<?php ActiveForm::end(); ?>

<script>
    $(document).on('week-view-calendar-select', function(event, params) {
        $('#enrolmentform-startdate').val(moment(params.date, "DD-MM-YYYY h:mm a").format('MMM D, Y')).trigger('change');
        $('#enrolmentform-day').val(moment(params.date, "DD-MM-YYYY h:mm a").format('dddd')).trigger('change');
        $('#enrolmentform-fromtime').val(moment(params.date, "DD-MM-YYYY h:mm a").format('HH:mm:ss')).trigger('change');
        return false;
    });

    $(document).on('click', '.modal-back', function () {
        $('#modal-spinner').show();
        $.ajax({
            url: '<?= Url::to(['course/basic-detail', 'studentId' => $student->id]) ?>',
            type: 'get',
            dataType: "json",
            data: $('#modal-form').serialize(),
            success: function (response)
            {
                if (response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('.modal-save').text('Next');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">New Enrolment</h4>');
                    $('#popup-modal .modal-dialog').css({'width': '600px'});
                    $('#customer-discount').val(response.customerDiscount);
                    $('#modal-spinner').hide();
                }
            }
        });
        return false;
    });
</script>
