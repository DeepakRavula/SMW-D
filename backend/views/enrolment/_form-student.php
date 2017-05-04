<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
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
<div class="wizard">
    <ul class="steps">
		<li><?= Html::a('Program', ['enrolment/create']); ?><span class="chevron"></span></li>
		<li><?= Html::a('Customer', ['enrolment/customer']); ?><span class="chevron"></span></li>
        <li class="active">Student<span class="chevron"></span></li>
        <li>Preview<span class="chevron"></span></li>
    </ul>
</div>
<div class="container">
	<?php $form = ActiveForm::begin(); ?>
		 <div class="form-group">
			<label class="col-sm-2 control-label">Name</label>
			<div class="col-sm-3">
				<?php
            echo $form->field($model, 'first_name')->textInput(['placeholder' => 'First Name'])->label(false); ?>
			</div>
			<div class="col-sm-3">
				<?php
            echo $form->field($model, 'last_name')->textInput(['placeholder' => 'Last Name'])->label(false); ?>
			</div>
		</div>
	<div class="clearfix"></div>
	 <div class="form-group">
		<label  class="col-sm-2 control-label">Date of Birth</label>
		<div class="col-sm-3">
		<?=  $form->field($model, 'birth_date')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class' => 'form-control'],
                ])->label(false); ?>
        </div>
	</div>
	<div class="clearfix"></div>
		<?php ActiveForm::end(); ?>
</div> <!-- ./container -->
