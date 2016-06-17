<?php

use common\models\User;
use yii\helpers\Html;
use common\models\City;
use common\models\Province;
use common\models\Country;
use common\models\Address;
use common\models\Location;
use common\models\PhoneNumber;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use wbraganca\selectivity\SelectivityWidget;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
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
<style>
    .box-body{
        padding-left: 0;
        padding-right: 0;
    }
	.quali-fields{
        display: none;
    }
    hr{
        margin:10px 0;
    }
    .form-well{
        margin-bottom: 10px;
        padding-top: 15px;
    }
    .address-fields, .phone-fields, .quali-fields label{
        display: none;
    }

</style>

<div class="user-form"> 

	<?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="row-fluid">
        <div class="col-md-4">
			<?php echo $form->field($model, 'firstname') ?>
        </div>
        <div class="col-md-4">
			<?php echo $form->field($model, 'lastname') ?>
        </div>
        <div class="col-md-4">
			<?php echo $form->field($model, 'email') ?>
        </div>
        <div class="clearfix"></div>
	</div>
    <hr class="hr-ad">

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
			<div class="clearfix"></div>
	<?php endforeach; ?>
			</div><!-- widgetContainer -->
    </div>
<?php DynamicFormWidget::end(); ?>


	<hr class="hr-ad">


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
			<h4 class="pull-left m-r-20">Phone</h4>
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
				<div class="clearfix"></div>
		<?php endforeach; ?>
				</div>
		</div>
		<hr class="hr-ph">
		<?php DynamicFormWidget::end(); ?>
        <!-- Qualification show hide -->
<?php $userRoles = Yii::$app->authManager->getRolesByUser($model->model->id);
$userRole = end($userRoles); ?>
<?php //if ( ! empty($userRole) && $userRole->name === User::ROLE_TEACHER):  ?>
<?php if (!empty($userRole) && $userRole->name === User::ROLE_TEACHER || $model->roles === User::ROLE_TEACHER): ?>
			<div class="row-fluid">
				<div class="col-md-12">
					<h4 class="pull-left m-r-20">Qualifications</h4>
					<a href="#" class="add-quali text-add-new"><i class="fa fa-plus-circle"></i> Add new qualification </a>
					<div class="clearfix"></div>
				</div>
				<div class="quali-fields form-well p-l-20">
					<!-- <h4>Choose qualifications</h4> -->
					<div class="row">
						<div class="col-md-12">
							<?=
							$form->field($model, 'qualifications')->widget(SelectivityWidget::classname(), [
								'pluginOptions' => [
									'allowClear' => true,
									'multiple' => true,
									'items' => $programs,
									'value' => $model->qualifications,
									'placeholder' => 'No qualification selected'
								]
							]);
							?>

						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<hr class="hr-qu">
<?php endif; ?>
        <div class="row-fluid">
			<div class="col-md-2">
			<?php if (!$model->getModel()->getIsNewRecord()) : ?>
			<?php echo $form->field($model, 'status')->dropDownList(User::statuses(), ['options' => [2 => ['Selected' => 'selected']]]) ?>
		<?php endif; ?>
			</div>
        </div>
        <div class="col-md-12">
<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>
<?php ActiveForm::end(); ?>
	</div> <!-- user-form -->
	<script>
		$('.add-address').bind('click', function () {
			$('.address-fields').show();
			$('.hr-ad').hide();
			setTimeout(function () {
				$('.add-address').addClass('add-item');
			}, 100);
		});
		$('.add-phone').bind('click', function () {
			$('.phone-fields').show();
			$('.hr-ph').hide();
			setTimeout(function () {
				$('.add-phone').addClass('add-item-phone');
			}, 100);
		});
		$('.add-quali').bind('click', function () {
			$('.quali-fields').show();
			$('.hr-qu').hide();
		});
	</script>
