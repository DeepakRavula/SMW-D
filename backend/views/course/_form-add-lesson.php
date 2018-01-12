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

		<i class="fa fa-book"></i> Lesson Schedule
		<button type="button" class="pull-right add-item btn btn-primary btn-xs"><i class="fa fa-plus"></i> Add Lesson</button>
		<div class="clearfix"></div> 
	<div class="container-items"><!-- widgetContainer -->
		<?php foreach ($courseSchedule as $index => $schedule): ?>
			<div class="item"><!-- widgetBody -->
				<div class="">
					<span class="panel-title-lesson">Lesson: <?= ($index + 1) ?></span>
					
					<div class="clearfix"></div>
				</div>
				<div class="">
                      <div class="col-md-12">
					<?php
					// necessary for update action.
					if (!$schedule->isNewRecord) {
						echo Html::activeHiddenInput($schedule, "[{$index}]id");
					}
					?>
					<div class="row">
						<div class="col-md-2 hand course-calendar-icon">
							<label class="control-label">Schedule</label>
							<span class="fa fa-calendar" style="font-size:25px; margin:5px 12px;"></span>
						</div>
						<div class="col-md-3 lesson-day">
							<?= $form->field($schedule, "[{$index}]day")
							->textInput(['maxlength' => true,
							'class' => 'day form-control',
							'readOnly' => true,
							]) ?>

						</div>
						<div class="col-md-5 lesson-time">
							<?= $form->field($schedule, "[{$index}]fromTime")
							->textInput(['maxlength' => true,
							'class' => 'time form-control',
							'readOnly' => true,
							])->label('Time') ?>
						</div>
                                            <div class="col-md-2 m-t-5">
                                            <button type="button" class="pull-right remove-item btn btn-danger m-t-25"><i class="fa fa-minus"></i></button>
                                            </div>
                                            </div><!-- end:row -->
                      </div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php DynamicFormWidget::end(); ?>