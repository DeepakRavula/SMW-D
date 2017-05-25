<?php

use common\models\PhoneNumber;
use common\models\Address;
use yii\helpers\ArrayHelper;
use common\models\City;
use common\models\Country;
use common\models\Province;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'New Enrolment';
?>
<div class="container">
		 <div class="form-group">
			<label class="col-sm-2 control-label">Name</label>
			<div class="col-sm-3">
				<?php
            echo $form->field($userProfile, 'firstname')->textInput(['placeholder' => 'First Name'])->label(false); ?>
			</div>
			<div class="col-sm-3">
				<?php
            echo $form->field($userProfile, 'lastname')->textInput(['placeholder' => 'Last Name'])->label(false); ?>
			</div>
		</div>
	<div class="clearfix"></div>
	 <div class="form-group">
		<label  class="col-sm-2 control-label">Email</label>
		<div class="col-sm-6">
		<?php
            echo $form->field($model, 'email')->textInput(['placeholder' => 'Email'])->label(false); ?>	
		</div>
		</div>
	<div class="clearfix"></div>
	 <div class="form-group">
		<label  class="col-sm-2 control-label">Phone Number</label>
		<div class="col-sm-3">
			<?= $form->field($phoneModel, 'number')->textInput(['placeholder' => 'Number'])->label(false); ?>
		</div>
		<div class="col-sm-1">
			<?= $form->field($phoneModel, 'extension')->textInput(['placeholder' => 'Ext'])->label(false); ?>
		</div>
		<div class="col-sm-2">
			<?= $form->field($phoneModel, 'label_id')->dropDownList(PhoneNumber::phoneLabels())->label(false);; ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label">Address</label>
		<div class="col-sm-3">
			<?= $form->field($addressModel, 'label')->dropDownList(Address::labels())->label(false); ?>
		</div>	
	</div>	
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label"></label>
		<div class="col-sm-3">
			<?= $form->field($addressModel, 'address')->textInput(['placeholder' => 'Street Address'])->label(false); ?>
		</div>	
	</div>	
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label"></label>
		<div class="col-sm-3">
			<?= $form->field($addressModel, 'city_id')->dropDownList(
                ArrayHelper::map(City::find()->all(), 'id', 'name'));?>
		</div>	
		<div class="col-sm-3">
			<?= $form->field($addressModel, 'province_id')->dropDownList(
                ArrayHelper::map(Province::find()->all(), 'id', 'name')); ?>
		</div>
	</div>	
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label"></label>
		<div class="col-sm-3">
			<?= $form->field($addressModel, 'country_id')->dropDownList(
                ArrayHelper::map(Country::find()->all(), 'id', 'name'));?>
		</div>	
		<div class="col-sm-3">
			<?= $form->field($addressModel, 'postal_code')->textInput(['placeholder' => 'Postal Code'])->label(false); ?>
		</div>
	</div>	
	<div class="clearfix"></div>
</div> <!-- ./container -->
