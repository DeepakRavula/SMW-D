<?php

use common\models\City;
use common\models\Country;
use common\models\Province;
use yii\helpers\ArrayHelper;
use common\models\Label;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'New Enrolment';
?>
			<div class="col-md-4">
				<?php
            echo $form->field($userProfile, 'firstname')->textInput(['placeholder' => 'First Name'])->label(false); ?>
			</div>
			<div class="col-md-4">
				<?php
            echo $form->field($userProfile, 'lastname')->textInput(['placeholder' => 'Last Name'])->label(false); ?>
			</div>
			<?= $form->field($phoneModel, 'number')->textInput(['placeholder' => 'Number'])->label(false); ?>
			<?= $form->field($phoneModel, 'extension')->textInput(['placeholder' => 'Ext'])->label(false); ?>
			<?=
	$form->field($phoneModel, "labelId")->widget(Select2::classname(), [
		'data' => ArrayHelper::map(Label::find()
				->andWhere(['userAdded' => false])
				->all(), 'id', 'name'),
		'options' => [
			'id' => 'phone-label',
			'placeholder' => 'Select Label'],
		'pluginOptions' => [
			'tags' => true,
		],
	])->label(false);
	?>
			<?= $form->field($userEmail, "email")->textInput(['maxlength' => true])->label(false) ?>
			<?=
		$form->field($userEmail, "labelId")->widget(Select2::classname(), [
			'data' => ArrayHelper::map(Label::find()
					->andWhere(['userAdded' => false])
					->all(), 'id', 'name'),
			'options' => ['placeholder' => 'Select Label'],
			'pluginOptions' => [
				'tags' => true,
			],
		])->label(false);
		?>
			<?=
	$form->field($addressModel, "labelId")->widget(Select2::classname(), [
		'data' => ArrayHelper::map(Label::find()
				->andWhere(['userAdded' => false])
				->all(), 'id', 'name'),
		'options' => [
			'id' => 'address-label',
			'placeholder' => 'Select Label'],
		'pluginOptions' => [
			'tags' => true,
		],
	])->label(false);
	?>
			<?= $form->field($addressModel, 'address')->textInput(['placeholder' => 'Street Address'])->label(false); ?>
			<?= $form->field($addressModel, 'cityId')->dropDownList(
                ArrayHelper::map(City::find()->all(), 'id', 'name'));?>
			<?= $form->field($addressModel, 'provinceId')->dropDownList(
                ArrayHelper::map(Province::find()->all(), 'id', 'name')); ?>
			<?= $form->field($addressModel, 'countryId')->dropDownList(
                ArrayHelper::map(Country::find()->all(), 'id', 'name'));?>
			<?= $form->field($addressModel, 'postalCode')->textInput(['placeholder' => 'Postal Code'])->label(false); ?>
