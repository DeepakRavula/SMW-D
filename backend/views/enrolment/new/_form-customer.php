<?php

use common\models\City;
use common\models\Country;
use common\models\Province;
use yii\helpers\ArrayHelper;
use common\models\Label;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

?>
<div class="user-create-form">
	<div class="row">
		<div class="col-xs-3">
			<label class="modal-form-label">Name</label>
		</div>
		<div class="col-xs-4">
			<?php echo $form->field($userProfile, 'firstname')->textInput(['placeholder' => 'First Name'])->label(false); ?>
		</div>
		<div class="col-xs-5">
			<?php echo $form->field($userProfile, 'lastname')->textInput(['placeholder' => 'Last Name'])->label(false); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-3">
			<label class="modal-form-label">Email</label>
		</div>	
		<div class="col-xs-4">
			<?= $form->field($userEmail, "email")->textInput(['placeholder' => 'Email', 'maxlength' => true])->label(false) ?>	
		</div>
		<div class="col-xs-5">
			<?=
				$form->field($userEmail, "labelId")->widget(Select2::classname(), [
					'data' => ArrayHelper::map(Label::find()
							->andWhere(['userAdded' => false])
							->all(), 'id', 'name'),
					'pluginOptions' => [
						'tags' => true,
					],
				])->label(false);
				?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-3">
			<label class="modal-form-label">Phone Number</label>
		</div>	
		<div class="col-xs-4">
		 <?= $form->field($phoneModel, 'number')->widget(MaskedInput::className(), [
         'mask' => '(999) 999-9999',
        ])->label(false); ?>
		</div>
		<div class="col-xs-2">
			<?= $form->field($phoneModel, 'extension')->textInput(['placeholder' => 'Ext'])->label(false); ?>
		</div>
		<div class="col-xs-3">
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
	])->label(false);
	?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-3">
        	<label class="modal-form-label">Address</label>
		</div>	
		<div class="col-xs-4">
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
	])->label(false);
	?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-3"></div>
		<div class="col-xs-9">
	<?= $form->field($addressModel, 'address')->textInput(['placeholder' => 'Street Address'])->label(false); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-3"></div>
		<div class="col-xs-4">
	<?= $form->field($addressModel, 'cityId')->dropDownList(
		ArrayHelper::map(City::find()->all(), 'id', 'name'))->label(false);
	?>
		</div>
		<div class="col-xs-5">
			<?= $form->field($addressModel, 'provinceId')->dropDownList(
				ArrayHelper::map(Province::find()->all(), 'id', 'name'))->label(false);
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-3"></div>
		<div class="col-xs-4">
			<?= $form->field($addressModel, 'countryId')->dropDownList(
				ArrayHelper::map(Country::find()->all(), 'id', 'name'))->label(false);
			?>
		</div>
		<div class="col-xs-5">
	<?= $form->field($addressModel, 'postalCode')->textInput(['placeholder' => 'Postal Code'])->label(false); ?>
		</div>
	</div>
	<div class="row">
		<div class="form-group pull-right">
			<?= Html::a('Cancel', '#', ['class' => 'm-r-10 btn btn-default new-enrol-cancel']); ?>
			<button class="step3-next btn btn-info pull-right" type="button" >Next</button>
		</div>
		<div class="form-group pull-left">
			<button class="step3-back btn btn-info" type="button" >Back</button>
		</div>
	</div>
</div>    