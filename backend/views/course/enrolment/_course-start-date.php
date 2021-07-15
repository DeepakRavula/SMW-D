<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\jui\DatePicker;
?>

<?php
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['course/create-enrolment-date-detail', 'studentId' => $student ? $student->id : null, 
            'isReverse' => $isReverse, 'EnrolmentForm' => $model])
    ]);
?>
<div class="user-create-form">
    <div class="row">
        <div class="col-md-8">
        <?= $form->field($model, 'startDate')->widget(DatePicker::classname(), [
            'options' => [
                'class' => 'form-control',
                'readOnly' => true,
            ],
            'dateFormat' => 'php:M d, Y',
            'clientOptions' => [
                'defaultDate' => (new \DateTime($model->startDate))->format('M d, Y'),
                'changeMonth' => true,
                'yearRange' => '1500:3000',
                'changeYear' => true,
            ]
            ])->label('Start Date');
        ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
        <?= $form->field($model, 'paymentCycleStartDate')->widget(DatePicker::classname(), [
            'options' => [
                'class' => 'form-control',
                'readOnly' => true,
            ],
            'dateFormat' => 'php:M d, Y',
            'clientOptions' => [
                'defaultDate' => (new \DateTime())->format('M d, Y'),
                'changeMonth' => true,
                'yearRange' => '1500:3000',
                'changeYear' => true,
            ]
            ])->label('Payment Cycle Effective Date');
        ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
        <?php echo $form->field($model, 'isOnline')->checkbox(); ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        $('#modal-back').show();
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Enrolment Start Date</h4>');
        $('.modal-save').show();
        $('.modal-save').text('Next');
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        $('#modal-spinner').hide();
        $('#modal-back').removeClass();
        $('#modal-back').addClass('btn btn-info course-date-detail-back');
    });

    $(document).off('click', '.course-date-detail-back').on('click', '.course-date-detail-back', function () {
        $('#modal-spinner').show();
        $.ajax({
            url: '<?= Url::to(['course/create-enrolment-basic', 'studentId' => !empty($student) ? $student->id : null,
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
</script>
