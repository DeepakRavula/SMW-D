<?php

use yii\bootstrap\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use common\models\Location;
use common\models\Address;
use common\models\City;
use common\models\Province;
use common\models\Country;
use yii\helpers\ArrayHelper;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */

$js = '
jQuery(".dynamicform_address").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_address .panel-title-address").each(function(index) {
        jQuery(this).html("Address: " + (index + 1))
    });
});

jQuery(".dynamicform_address").on("afterDelete", function(e) {
    jQuery(".dynamicform_address .panel-title-address").each(function(index) {
        jQuery(this).html("Address: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>

<?php
	DynamicFormWidget::begin([
		'widgetContainer' => 'dynamicform_address', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
		'widgetBody' => '.address-container-items', // required: css class selector
		'widgetItem' => '.address-item', // required: css class
		'limit' => 10, // the maximum times, an element can be cloned (default 999)
		'min' => 0, // 0 or 1 (default 1)
		'insertButton' => '.address-add-item', // css class
		'deleteButton' => '.address-remove-item', // css class
		'model' => $addressModels[0],
		'formId' => 'dynamic-form',
		'formFields' => [
			'addresslabel',
			'address',
			'city',
			'country',
			'province',
			'postalcode',
		],
	]);
	?>
    <div class="row-fluid">
		<div class="col-md-12">
			<h4 class="pull-left m-r-20">Address</h4>
			<a href="#" class="add-address text-add-new address-add-item"><i class="fa fa-plus-circle"></i> Add new address</a>
			<div class="clearfix"></div>
		</div>
		<div class="address-container-items address-fields form-well">
<?php foreach ($addressModels as $index => $addressModel): ?>
				<div class="item-block address-item"><!-- widgetBody -->
					<h4>
						<span class="panel-title-address">Address: <?= ($index + 1) ?></span>
						<button type="button" class="pull-right address-remove-item btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>
						<div class="clearfix"></div>
					</h4>
					<?php
					if (!$addressModel->isNewRecord) {
						echo Html::activeHiddenInput($addressModel, "[{$index}]id");
					}
					$locationModel = Location::findOne(['id' => Yii::$app->session->get('location_id')]);
					?>

					<div class="row">
						<div class="col-sm-4">
							<?= $form->field($addressModel, "[{$index}]label")->dropDownList(Address::labels(), ['prompt' => 'Select Label']) ?>
						</div>
						<div class="col-sm-4">
							<?= $form->field($addressModel, "[{$index}]address")->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-sm-4">
							<?=
							$form->field($addressModel, "[{$index}]city_id")->dropDownList(
									ArrayHelper::map(City::find()->all(), 'id', 'name'), ['options' => [
									$locationModel->city_id => ['selected' => true],
								]
							])
							?>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<?=
							$form->field($addressModel, "[{$index}]country_id")->dropDownList(
									ArrayHelper::map(Country::find()->all(), 'id', 'name'), ['options' => [
									$locationModel->country_id => ['selected' => true],
								]
							])
							?>
						</div>
						<div class="col-sm-4">
							<?=
							$form->field($addressModel, "[{$index}]province_id")->dropDownList(
									ArrayHelper::map(Province::find()->all(), 'id', 'name'), ['options' => [
									$locationModel->province_id => ['selected' => true],
								]
							])
							?>
						</div>
						<div class="col-sm-4">
	<?= $form->field($addressModel, "[{$index}]postal_code")->textInput(['maxlength' => true]) ?>
						</div>

						<div class="clearfix"></div>
					</div><!-- end row -->
				</div><!-- widgetBody -->
	<?php endforeach; ?>
			</div><!-- widgetContainer -->
    </div>
<?php DynamicFormWidget::end(); ?>
<div class="clearfix"></div>