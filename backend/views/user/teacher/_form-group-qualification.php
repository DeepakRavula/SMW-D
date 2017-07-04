<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use common\models\Qualification;
use kartik\select2\Select2;
use common\models\Program;
use yii\helpers\ArrayHelper;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */

$js = '
jQuery(".group_dynamicform_qualification").on("afterInsert", function(e, item) {
    jQuery(".group_dynamicform_qualification .panel-title-qualification").each(function(index) {
        jQuery(this).html("Group Programs: " + (index + 1))
    });
});

jQuery(".group_dynamicform_qualification").on("afterDelete", function(e) {
    jQuery(".group_dynamicform_qualification .panel-title-qualification").each(function(index) {
        jQuery(this).html("Group Programs: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>
<?php
DynamicFormWidget::begin([
	'widgetContainer' => 'group_dynamicform_qualification', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
	'widgetBody' => '.group-qualification-container-items', // required: css class selector
	'widgetItem' => '.group-qualification-item', // required: css class
	'limit' => 10, // the maximum times, an element can be cloned (default 999)
	'min' => 0, // 0 or 1 (default 1)
	'insertButton' => '.group-qualification-add-item', // css class
	'deleteButton' => '.group-qualification-remove-item', // css class
	'model' => $qualificationModels[0],
	'formId' => 'dynamic-form',
	'formFields' => [
		'program_id',
		'rate',
		'type',
	],
]);
?>
<div class="row-fluid">
	<div class="col-md-12">
		<h4 class="pull-left m-r-20">Group Programs</h4>
		<a href="#" class="add-group-qualification text-add-new group-qualification-add-item"><i class="fa fa-plus"></i></a>
		<div class="clearfix"></div>
	</div>
	<div class="group-qualification-container-items group-qualification-fields form-well">
		<?php foreach ($qualificationModels as $index => $qualificationModel): ?>
			<div class="item-block group-qualification-item"><!-- widgetBody -->
				<h4>
					<span class="panel-title-qualification pull-left">Group Programs: <?= ($index + 1) ?></span>
					<button type="button" class="pull-right group-qualification-remove-item btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>
					<div class="clearfix"></div>
				</h4>
				<?php
				if (!$qualificationModel->isNewRecord) {
					echo Html::activeHiddenInput($qualificationModel, "[{$index}][group]id");
				}
				$groupPrograms = ArrayHelper::map(Program::find()->group()->active()->all(), 'id', 'name'); 
				?>
				<div class="row">
					<div class="col-sm-4">
						<?= $form->field($qualificationModel, "[{$index}][group]program_id")->widget(Select2::classname(), [
    'data' => $groupPrograms,
    'options' => ['placeholder' => 'Select a group program ...'],
    
]);?>
					</div>
					<div class="col-sm-4">
	<?= $form->field($qualificationModel, "[{$index}][group]rate")->textInput(['maxlength' => true]) ?>
					</div>
					<div class="clearfix"></div>
				</div>
			</div><!-- widgetBody -->
<?php endforeach; ?>
	</div><!-- widgetContainer -->
</div>
<div class="clearfix"></div>
<hr class="hr-quali right-side-faded">
<?php DynamicFormWidget::end(); ?>