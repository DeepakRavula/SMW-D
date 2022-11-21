<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Student;
use yii\helpers\Url;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php 
        $url = Url::to(['private-lesson/teacher-bulk-reschedule']);
    ?>
<div class="row teacher-bulk-reschedule-form">
<div class="col-md-12">
<div id="teacher-bulk-reschedule-error" class="alert alert-danger" style="display:none;"></div>
</div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',

        ]); ?>

<div class="col-md-6">
    <?= $form->field($model, 'teacherBulkRescheduleSourceDate')->widget(
        DatePicker::classname(), [
            'dateFormat' => 'php:M d, Y',
            'options' => [
                'class' => 'form-control',
                'readOnly' => true,
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => '1500:3000',
                'changeYear' => true
            ]
        ])->label('Source Date');
    ?>
</div><div class="col-md-6">
    <?= $form->field($model, 'teacherBulkRescheduleDestinationDate')->widget(
        DatePicker::classname(), [
            'dateFormat' => 'php:M d, Y',
            'options' => [
                'class' => 'form-control',
                'readOnly' => true,
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => '1500:3000',
                'changeYear' => true
            ]
        ])->label('Destination Date');
    ?>
</div>
<div class = "row">
<?= $form->field($model, 'selectedTeacherId')->hiddenInput(['value' => $model->selectedTeacherId])->label(false);
    ?>
</div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width' : '400'})
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Bulk Reschedule</h4>');
    });

    $(document).on('modal-error', function (event, params) {
        if (params.error) {
            $('#teacher-bulk-reschedule-error').html(params.error).fadeIn().delay(5000).fadeOut();
        }
    });
</script>