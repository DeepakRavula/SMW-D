<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Label;
use common\models\Address;
use common\models\City;
use common\models\Country;
use common\models\Province;
use kartik\select2\Select2Asset;
use yii\helpers\Url;

Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<style>
	
</style>
<div class="row user-create-form">
 <?php $form = ActiveForm::begin([
		'action' => Url::to(['user/create', 'role_name' => $searchModel->role_name]),
		'id' => 'user-form',
		]); ?>
	<fieldset class="col-md-12">    	
		<legend>Basic</legend>
		<div class="col-md-4">
			<?php echo $form->field($model, 'firstname') ?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'lastname') ?>		
		</div>	
		<div class="col-md-4">
			<?= $form->field($emailModels, 'email')->textInput(['maxlength' => true]) ?>
		</div>	
		<div class="col-md-4">
			<?= $form->field($emailModels, 'labelId')->widget(Select2::classname(), [
				'data' => ArrayHelper::map(Label::find()
					->user($model->getModel()->id)
					->all(), 'id', 'name'),
				'options' => ['placeholder' => 'Select Label'],
				'pluginOptions' => [
					'tags' => true,
					'allowClear' => true,
				],
		])->label('Label');
		?>
		</div>	
	</fieldset>	
	<fieldset class="col-md-12">    	
		<legend>Phone</legend>
<div class="col-md-4">
	<?= $form->field($phoneNumberModels, "number")->textInput(['maxlength' => true]) ?>
</div>
<div class="col-md-4">
	<?=
	$form->field($phoneNumberModels, "label_id")->widget(Select2::classname(), [
		'data' => ArrayHelper::map(Label::find()
				->user($model->getModel()->id)
				->all(), 'id', 'name'),
		'options' => ['placeholder' => 'Select Label'],
		'pluginOptions' => [
			'tags' => true,
			'allowClear' => true,
		],
	])->label('Label');
	?>
</div>
<div class="col-md-4">
<?= $form->field($phoneNumberModels, "extension")->textInput(['maxlength' => true]) ?>
</div>
		</fieldset>
		<fieldset class="col-md-12">    	
		<legend>Address</legend>
<div class="col-md-4">
<?= $form->field($addressModels, "label")->dropDownList(Address::labels(), ['prompt' => 'Select Label']) ?>
</div>
<div class="col-md-4">
<?= $form->field($addressModels, "address")->textInput(['maxlength' => true]) ?>
</div>
<div class="col-md-4">
	<?=
	$form->field($addressModels, "city_id")->dropDownList(
		ArrayHelper::map(City::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'), ['class' => 'city form-control'])
	?>
</div>
<div class="col-md-4">
	<?=
	$form->field($addressModels, "country_id")->dropDownList(
		ArrayHelper::map(Country::find()->all(), 'id', 'name'), ['class' => 'country form-control'])
	?>
</div>
<div class="col-md-4">
	<?=
	$form->field($addressModels, "province_id")->dropDownList(
		ArrayHelper::map(Province::find()->all(), 'id', 'name'), ['class' => 'province form-control'])
	?>
</div>
<div class="col-md-4">
<?= $form->field($addressModels, "postal_code")->textInput(['maxlength' => true]) ?>
</div>
		</fieldset>
<div class="row col-md-12">
	<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'pull-right btn btn-info', 'name' => 'signup-button']) ?>
	<?php
		echo Html::a('Cancel', '#', ['class' => 'pull-right m-r-10 btn user-add-cancel btn-default']);
	?>
</div>
<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function() {
     $(document).on('beforeSubmit', '#user-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
						}else
				{
				 $('#user-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});
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

	$('#user-update-tab a').click(function (e) {
		$('.section-tab').css('display', 'block');
		//$('#contact-section').css('display','block');
		e.preventDefault();
		$(this).tab('show');
	});
	$('.nav-tabs a').on('shown.bs.tab', function (e) {
		$('input[name="UserForm[section]"]').val(e.target.hash);
	});
});
</script>
