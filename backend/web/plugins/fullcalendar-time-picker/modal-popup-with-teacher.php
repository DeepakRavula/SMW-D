<?php

use yii\bootstrap\Modal;
use common\models\Lesson;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

?>

<script type="text/javascript" src="/admin/plugins/fullcalendar-time-picker/fullcalendar-time-picker.js?v=8"></script>
<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Choose Date, Day and Time</h4>',
        'id' => 'calendar-date-time-picker-modal',
        'footer' => $this->render('/layouts/time-picker-footer')
    ]);
?>
<div id="calendar-date-time-picker-error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="row">
    
    <?php $lessonModel = new Lesson();
    $form = ActiveForm::begin([
       'id' => 'lesson-form'
    ]); ?>
    <div class="col-md-6">
        <?= $form->field($lessonModel, 'teacherId')->widget(Select2::classname(), [
            'data' => null,
            'options' => [
                'placeholder' => 'Select Substitute Teacher',
                'id' => 'calendar-date-time-picker-teacher'
            ]
        ])->label('Substitute Teacher'); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($lessonModel, 'date')->textInput([
            'id' => 'calendar-date-time-picker-date', 'readOnly' => true,
        ])->label('Reschedule Date');?>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="col-lg-12">
        <div id="calendar-date-time-picker" ></div>
    </div>   
</div>
<?php Modal::end(); ?>

<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
</script>

