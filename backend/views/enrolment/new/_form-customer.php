<?php

use common\models\City;
use common\models\Country;
use common\models\Province;
use yii\helpers\ArrayHelper;
use common\models\Label;
use kartik\select2\Select2;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'New Enrolment';
?>
<div class="row user-create-form">
	<div class="col-md-6">
		<?php echo $form->field($userProfile, 'firstname')->textInput(); ?>
	</div>
	<div class="col-md-6">
		<?php echo $form->field($userProfile, 'lastname')->textInput(); ?>
	</div>
	<div class="col-md-6">
<?= $form->field($phoneModel, 'number')->textInput(); ?>
	</div>
	<div class="col-md-6">
<?= $form->field($userEmail, "email")->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-md-6">
<?=
$form->field($userEmail, "labelId")->widget(Select2::classname(), [
	'data' => ArrayHelper::map(Label::find()
			->andWhere(['userAdded' => false])
			->all(), 'id', 'name'),
	'pluginOptions' => [
		'tags' => true,
	],
])->label('Label');
?>
	</div>
	<div class="col-md-4">
<?=
$form->field($phoneModel, "labelId")->widget(Select2::classname(), [
	'data' => ArrayHelper::map(Label::find()
			->andWhere(['userAdded' => false])
			->all(), 'id', 'name'),
	'options' => [
		'id' => 'phone-label',
	],
	'pluginOptions' => [
		'tags' => true,
	],
])->label('Label');
?>
	</div>
	<div class="col-md-2">
<?= $form->field($phoneModel, 'extension')->textInput(); ?>
	</div>
	
	<div class="col-md-4">
<?=
$form->field($addressModel, "labelId")->widget(Select2::classname(), [
	'data' => ArrayHelper::map(Label::find()
			->andWhere(['userAdded' => false])
			->all(), 'id', 'name'),
	'options' => [
		'id' => 'address-label',
	],
	'pluginOptions' => [
		'tags' => true,
	],
])->label('Label');
?>
	</div>
	<div class="col-md-4">
<?= $form->field($addressModel, 'address')->textInput(['placeholder' => 'Street Address']); ?>
	</div>
	<div class="col-md-4">
<?= $form->field($addressModel, 'cityId')->dropDownList(
	ArrayHelper::map(City::find()->all(), 'id', 'name'));
?>
	</div>
	<div class="col-md-4">
		<?= $form->field($addressModel, 'provinceId')->dropDownList(
			ArrayHelper::map(Province::find()->all(), 'id', 'name'));
		?>
	</div>
	<div class="col-md-4">
		<?= $form->field($addressModel, 'countryId')->dropDownList(
			ArrayHelper::map(Country::find()->all(), 'id', 'name'));
		?>
	</div>
	<div class="col-md-4">
<?= $form->field($addressModel, 'postalCode')->textInput(['placeholder' => 'Postal Code']); ?>
	</div>
	<div class="form-group pull-right">
		<?= Html::a('Cancel', '#', ['class' => 'm-r-10 btn btn-default new-enrol-cancel']); ?>
		<button class="nextBtn btn btn-info pull-right" type="button" >Next</button>
	</div>