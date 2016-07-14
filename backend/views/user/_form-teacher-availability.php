<?php

use common\models\TeacherAvailability;
use kartik\time\TimePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TeacherAvailability */
/* @var $form yii\bootstrap\ActiveForm */

$js = '
jQuery(".dynamicform_availability").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_availability .panel-title-availability").each(function(index) {
        jQuery(this).html("Availability: " + (index + 1))
    });
});

jQuery(".dynamicform_availability").on("afterDelete", function(e) {
    jQuery(".dynamicform_availability .panel-title-availability").each(function(index) {
        jQuery(this).html("Availability: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>
<?php
	DynamicFormWidget::begin([
		'widgetContainer' => 'dynamicform_availability', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
		'widgetBody' => '.availability-container-items', // required: css class selector
		'widgetItem' => '.availability-item', // required: css class
		'limit' => 10, // the maximum times, an element can be cloned (default 999)
		'min' => 0, // 0 or 1 (default 1)
		'insertButton' => '.availability-add-item', // css class
		'deleteButton' => '.availability-remove-item', // css class
		'model' => $availabilityModels[0],
		'formId' => 'dynamic-form',
		'formFields' => [
			'day',
			'fromTime',
			'toTime',
		],
	]);
	?>
	<div class="row-fluid">
		<div class="col-md-12">
			<h4 class="pull-left m-r-20">Teacher Availability</h4>
			<a href="#" class="add-availability text-add-new availability-add-item"><i class="fa fa-plus"></i></a>
			<div class="clearfix"></div>
		</div>
		<div class="availability-container-items availability-fields form-well">
<?php foreach ($availabilityModels as $index => $availabilityModel): ?>
				<div class="item-block availability-item"><!-- widgetBody -->
					<h4>
						<span class="panel-title-availability">Availability: <?= ($index + 1) ?></span>
						<button type="button" class="pull-right availability-remove-item btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>
						<div class="clearfix"></div>
					</h4>
					<?php
					// necessary for update action.
					if (!$availabilityModel->isNewRecord) {
						echo Html::activeHiddenInput($availabilityModel, "[{$index}]id");
					}
					?>

	                <div class="row">
	                    <div class="col-sm-4">
	<?= $form->field($availabilityModel, "[{$index}]day")->dropdownList(TeacherAvailability::getWeekdaysList(),['prompt' => 'select day']) ?>
	                    </div>
	                    <div class="col-sm-4">
<?php
	$fromTime = \DateTime::createFromFormat('H:i:s', $availabilityModel->from_time);
	$toTime = \DateTime::createFromFormat('H:i:s', $availabilityModel->to_time);
	$availabilityModel->from_time = ! empty($availabilityModel->from_time) ? $fromTime->format('g:i A') : null;
	$availabilityModel->to_time = ! empty($availabilityModel->to_time) ? $toTime->format('g:i A') : null;
?>
	<?= $form->field($availabilityModel, "[{$index}]from_time")->widget(TimePicker::classname(), []); ?>
	                    </div>
	                    <div class="col-sm-4">
	<?= $form->field($availabilityModel, "[{$index}]to_time")->widget(TimePicker::classname(), []) ?>
	                    </div>
	                    <div class="clearfix"></div>
	                </div>
				</div>
		<?php endforeach; ?>
				</div>
		</div>
    <div class="clearfix"></div>
		<?php DynamicFormWidget::end(); ?>
