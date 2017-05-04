<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'New Enrolment';
?>
<div class="wizard">
    <ul class="steps">
		<li><?= Html::a('Program', ['enrolment/create']); ?><span class="chevron"></span></li>
        <li class="active">Customer<span class="chevron"></span></li>
        <li>Student<span class="chevron"></span></li>
        <li>Preview<span class="chevron"></span></li>
    </ul>
</div>
<div class="container">
	<?php $form = ActiveForm::begin(); ?>
		 <div class="form-group">
			<label class="col-sm-2 control-label">Name</label>
			<div class="col-sm-4">
				<?php
            echo $form->field($userProfile, 'firstname')->textInput(['placeholder' => 'First Name']); ?>
			</div>
			<div class="col-sm-4">
				<?php
            echo $form->field($userProfile, 'lastname')->textInput(['placeholder' => 'Last Name']); ?>
			</div>
		</div>
	<div class="clearfix"></div>
	 <div class="form-group">
		<label  class="col-sm-2 control-label">Email</label>
		<div class="col-sm-3">
			
		</div>
		<label  class="col-sm-2 control-label"></label>
		<div class="col-sm-1">
            <span class="fa fa-calendar fa-4"></span>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Teacher</label>
		<div class="col-sm-5">
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Day, Time & Duration</label>
		<div class="col-sm-5">
		Tuesdays @ 5.00pm	
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label">Payment Frequency</label>
		<div class="col-sm-3">
				
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label">Discount</label>
		<div class="col-sm-1">
		</div>
		<span class="col-sm-1">%</span>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Rate Per Lesson</label>
		<div class="col-sm-5" id="rate"></div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group">
		<label  class="col-sm-2 control-label p-10">Rate Per Month</label>
		<div class="col-sm-5" id="monthly-rate"></div>
	</div>
	<div class="clearfix"></div>
		<?php ActiveForm::end(); ?>
</div> <!-- ./container -->
