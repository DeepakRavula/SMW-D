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
jQuery(".dynamicform_qualification").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_qualification .panel-title-qualification").each(function(index) {
        jQuery(this).html("Qualification: " + (index + 1))
    });
});

jQuery(".dynamicform_qualification").on("afterDelete", function(e) {
    jQuery(".dynamicform_qualification .panel-title-qualification").each(function(index) {
        jQuery(this).html("Qualification: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>
<?php
DynamicFormWidget::begin([
	'widgetContainer' => 'dynamicform_qualification', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
	'widgetBody' => '.qualification-container-items', // required: css class selector
	'widgetItem' => '.qualification-item', // required: css class
	'limit' => 10, // the maximum times, an element can be cloned (default 999)
	'min' => 0, // 0 or 1 (default 1)
	'insertButton' => '.qualification-add-item', // css class
	'deleteButton' => '.qualification-remove-item', // css class
	'model' => $privateQualificationModels[0],
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
		<h4 class="pull-left m-r-20">Private Programs</h4>
		<a href="#" class="add-qualification text-add-new qualification-add-item"><i class="fa fa-plus"></i></a>
		<div class="clearfix"></div>
	</div>
	<div class="qualification-container-items qualification-fields form-well">
		<?php foreach ($privateQualificationModels as $index => $privateQualificationModel): ?>
			<div class="item-block qualification-item"><!-- widgetBody -->
				<h4>
					<span class="panel-title-qualification pull-left">Qualification: <?= ($index + 1) ?></span>
					<button type="button" class="pull-right qualification-remove-item btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>
					<div class="clearfix"></div>
				</h4>
				<?php
				if (!$privateQualificationModel->isNewRecord) {
					echo Html::activeHiddenInput($privateQualificationModel, "[{$index}]id");
				}
				$privatePrograms = ArrayHelper::map(Program::find()->privateProgram()->all(), 'id', 'name'); 
				?>
				<div class="row">
					<div class="col-sm-4">
						<?= $form->field($privateQualificationModel, "[{$index}]program_id")->dropDownList($privatePrograms);?>
					</div>
					<div class="col-sm-4">
	<?= $form->field($privateQualificationModel, "[{$index}]rate")->textInput(['maxlength' => true]) ?>
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