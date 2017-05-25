<?php

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
</div> <!-- ./container -->
