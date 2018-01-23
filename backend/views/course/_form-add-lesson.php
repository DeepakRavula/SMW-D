<?php

use wbraganca\dynamicform\DynamicFormWidget;
use kartik\time\TimePicker;

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-lesson").each(function(index) {
        jQuery(this).html("Lesson: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-lesson").each(function(index) {
        jQuery(this).html("Lesson: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>

<?php
DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper',
    'widgetBody' => '.container-items',
    'widgetItem' => '.item',
    'limit' => 7,
    'min' => 1,
    'insertButton' => '.add-item',
    'deleteButton' => '.remove-item',
    'model' => $courseSchedule[0],
    'formId' => 'group-course-form',
    'formFields' => [
        'day',
        'fromTime',
        'duration',
    ],
]);
?>



<div class="clearfix"></div> 
<div class="container-items">
    <div class="row">
        <div class="col-md-2 hand course-calendar-icon">
            <label class="control-label">Schedule</label>
        </div>
        <div class="col-md-3 lesson-day">
            <label class="control-label">Day</label>

        </div>
        <div class="col-md-4 lesson-time">
            <label class="control-label">Time</label>
        </div>
        <div class="col-md-3">
            <button type="button" class="add-item btn btn-info btn-social-icon btn-sm"><i class="fa fa-plus"></i></button>
        </div>
    </div><!-- widgetContainer -->
    <?php foreach ($courseSchedule as $index => $schedule): ?>
        <div class="item"><!-- widgetBody -->

            <div class="">
                <?php
                // necessary for update action.
                if (!$schedule->isNewRecord) {
                    echo Html::activeHiddenInput($schedule, "[{$index}]id");
                }
                ?>

                <div class="row m-t-10">
                    <div class="col-md-2 hand course-calendar-icon">
                        <span class="fa fa-calendar" style="font-size:25px; margin:5px 12px;"></span>
                    </div>
                    <div class="col-md-3 lesson-day">
                        <?=
                            $form->field($schedule, "[{$index}]day")
                            ->textInput(['maxlength' => true,
                                'class' => 'day form-control',
                                'readOnly' => true,
                            ])->label(false)
                        ?>

                    </div>
                    <div class="col-md-4 lesson-time">
                        <?=
                            $form->field($schedule, "[{$index}]fromTime")
                            ->textInput(['maxlength' => true,
                                'class' => 'time form-control',
                                'readOnly' => true,
                            ])->label(false) ?>
                    </div>
                                            <div class="col-md-3">
                        <button type="button" class="remove-item btn btn-danger btn-social-icon m-r-10 btn-sm"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- end:row -->
            </div>
        </div>
<?php endforeach; ?>
</div>
<?php DynamicFormWidget::end(); ?>