<?php

use yii\bootstrap\Modal;
use common\models\Lesson;
use yii\widgets\ActiveForm;

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
        <?= $form->field($lessonModel, 'date')->hiddenInput([
            'id' => 'calendar-date-time-picker-date',
        ])->label(false);?>
        <?= $form->field($lessonModel, 'teacherId')->hiddenInput([
            'id' => 'calendar-date-time-picker-teacher',
        ])->label(false);?>
        <?php ActiveForm::end(); ?>
    <div class="col-lg-12 pull-right">
        <div id="calendar-date-time-picker" ></div>
    </div>
</div>
<?php Modal::end(); ?>



