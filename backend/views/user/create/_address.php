<?php

use yii\helpers\Html;
use common\models\Location;
use common\models\City;
use common\models\Label;
use kartik\select2\Select2;
use common\models\Province;
use common\models\Country;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<div class="row user-create-form">
	<?php
	$form = ActiveForm::begin([
			'id' => 'address-form',
			'action' => Url::to(['user-contact/create-address', 'id' => $model->id])
	]);
	?>
	<div class="row">
		<?php
		$locationModel = Location::findOne(['id' => Yii::$app->session->get('location_id')]);
		?>
		<?=
		$form->field($userContact, "labelId")->widget(Select2::classname(), [
			'data' => ArrayHelper::map(Label::find()
					->user($model->id)
					->all(), 'id', 'name'),
			'options' => [
				'id' => 'address-label',
				'placeholder' => 'Select Label'],
			'pluginOptions' => [
				'tags' => true,
				'allowClear' => true,
			],
		])->label('Label');
		?>
		<?= $form->field($addressModel, "address")->textInput(['maxlength' => true]) ?>
		<?=
		$form->field($addressModel, "cityId")->dropDownList(
			ArrayHelper::map(City::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'), ['class' => 'city form-control'])
		?>
		<?=
		$form->field($addressModel, "countryId")->dropDownList(
			ArrayHelper::map(Country::find()->all(), 'id', 'name'), ['class' => 'country form-control'])
		?>
		<?=
		$form->field($addressModel, "provinceId")->dropDownList(
			ArrayHelper::map(Province::find()->all(), 'id', 'name'), ['class' => 'province form-control'])
		?>
		<?= $form->field($addressModel, "postalCode")->textInput(['maxlength' => true]) ?>
    </div>
	<div class="row pull-right">
		<?php echo Html::a('Cancel', '#', ['class' => 'btn btn-default address-cancel-btn']); ?>
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
<script>
 $(document).ready(function () {
	$(document).on('click', '.address-add-item', function () {
		var cityId = '<?= $locationModel->city_id; ?>';
		var countryId = '<?= $locationModel->country_id; ?>';
		var ProvinceId = '<?= $locationModel->province_id; ?>';
		$('.address-city').find('.city').val(cityId);
		$('.address').find('.country').val(countryId);
		$('.address').find('.province').val(ProvinceId);
		});
	});
</script>