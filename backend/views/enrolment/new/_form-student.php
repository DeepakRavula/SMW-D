<?php

use yii\jui\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

?>
<?php
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['reverse-enrolment/add-student', 'EnrolmentForm' => $courseDetail])
    ]);
?>
<div class="row user-create-form">
    <div class="col-xs-5">
        <label class="modal-customer-label">First Name</label>
    </div>
    <div class="col-xs-7">
        <?= $form->field($courseDetail, 'first_name')->textInput(['placeholder' => 'First Name'])->label(false); ?>
    </div>
    <div class="col-xs-5">
        <label class="modal-customer-label">Last Name</label>
    </div>
    <div class="col-xs-7">
        <?= $form->field($courseDetail, 'last_name')->textInput(['placeholder' => 'Last Name'])->label(false); ?>
    </div>
    <div class="col-xs-5">
        <label class="modal-customer-label">Birth Date</label>
    </div>
    <div class="col-xs-7">
       <?= $form->field($courseDetail, 'birth_date')->widget(DatePicker::className(), [
            'dateFormat' => 'php:M d, Y',
            'options' => [
                'class' => 'form-control',
                'readOnly' => true
            ],
            'clientOptions' => [
                'changeMonth' => true,
        'changeYear' => true,
                'yearRange' => '-70:+0',
            ],
        ])->textInput(['placeholder' => 'Select Date'])->label(false);?>
    </div>
    <div class="col-xs-5">
        <label class="modal-customer-label">Gender</label>
    </div>
    <div class="col-xs-7">
    <?php $list = [0 => 'Not Specified', 1 => 'Male', 2 => 'Female']; ?>
    <?php $courseDetail->gender = 0;  ?>
    <?= $form->field($courseDetail, 'gender')->radioList($list)->label(false); ?>
    </div>
</div> <!-- ./container -->
<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Student Details</h4>');
        $('.modal-save').show();
        $('.modal-save').text('Preview Lessons');
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        $('#modal-spinner').hide();
    });

    $(document).off('click', '.add-customer-back').on('click', '.add-customer-back', function () {
        $('#modal-spinner').show();
        $.ajax({
            url: '<?= Url::to(['reverse-enrolment/add-customer', 'EnrolmentForm' => $courseDetail]) ?>',
            type: 'get',
            dataType: "json",
            data: $('#modal-form').serialize(),
            success: function (response)
            {
                if (response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#modal-spinner').hide();
                }
            }
        });
        return false;
    });
</script>