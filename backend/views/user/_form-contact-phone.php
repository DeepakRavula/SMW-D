<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use common\models\PhoneNumber;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */

$js = '
jQuery(".dynamicform_phone").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_phone .panel-title-phone").each(function(index) {
        jQuery(this).html("Phone: " + (index + 1))
    });
});

jQuery(".dynamicform_phone").on("afterDelete", function(e) {
    jQuery(".dynamicform_phone .panel-title-phone").each(function(index) {
        jQuery(this).html("Phone: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>
<?php
	DynamicFormWidget::begin([
		'widgetContainer' => 'dynamicform_phone', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
		'widgetBody' => '.phone-container-items', // required: css class selector
		'widgetItem' => '.phone-item', // required: css class
		'limit' => 10, // the maximum times, an element can be cloned (default 999)
		'min' => 0, // 0 or 1 (default 1)
		'insertButton' => '.phone-add-item', // css class
		'deleteButton' => '.phone-remove-item', // css class
		'model' => $phoneNumberModels[0],
		'formId' => 'dynamic-form',
		'formFields' => [
			'phonenumber',
			'phonelabel',
			'phoneextension',
		],
	]);
	?>
	<div class="row-fluid">
		<div class="col-md-12">
			<h4 class="pull-left m-r-20">Phone Numbers</h4>
			<a href="#" class="add-phone text-add-new phone-add-item"><i class="fa fa-plus-circle"></i> Add new phone</a>
			<div class="clearfix"></div>
		</div>
		<div class="phone-container-items phone-fields form-well">
<?php foreach ($phoneNumberModels as $index => $phoneNumberModel): ?>
				<div class="item-block phone-item"><!-- widgetBody -->
					<h4>
						<span class="panel-title-phone">Phone Number: <?= ($index + 1) ?></span>
						<button type="button" class="pull-right phone-remove-item btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>
						<div class="clearfix"></div>
					</h4>
					<?php
					// necessary for update action.
					if (!$phoneNumberModel->isNewRecord) {
						echo Html::activeHiddenInput($phoneNumberModel, "[{$index}]id");
					}
					?>

	                <div class="row">
	                    <div class="col-sm-4">
	<?= $form->field($phoneNumberModel, "[{$index}]number")->textInput(['maxlength' => true]) ?>
	                    </div>
	                    <div class="col-sm-4">
	<?= $form->field($phoneNumberModel, "[{$index}]label_id")->dropDownList(PhoneNumber::phoneLabels(), ['prompt' => 'Select Label']) ?>
	                    </div>
	                    <div class="col-sm-4">
	<?= $form->field($phoneNumberModel, "[{$index}]extension")->textInput(['maxlength' => true]) ?>
	                    </div>
	                    <div class="clearfix"></div>
	                </div>
				</div>
		<?php endforeach; ?>
				</div>
		</div>
    <div class="clearfix"></div>
		<hr class="hr-ph">
		<?php DynamicFormWidget::end(); ?>