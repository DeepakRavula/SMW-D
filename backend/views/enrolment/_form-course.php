<?php

use common\models\Program;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use common\models\PaymentFrequency;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'New Enrolment';
?>
<div class="wizard">
    <ul class="steps">
        <li class="active">Program<span class="chevron"></span></li>
        <li>Customer<span class="chevron"></span></li>
        <li>Student<span class="chevron"></span></li>
        <li>Preview<span class="chevron"></span></li>
    </ul>
</div>
<div class="container">
	<?php $form = ActiveForm::begin(); ?>
		<div class="form-group">
			<label for="firstName" class="col-sm-3 control-label">Program</label>
			<div class="col-sm-7">
				<?php
            echo $form->field($model, 'programId')->dropDownList(
                ArrayHelper::map(Program::find()
					->active()
					->all(), 'id', 'name'), ['prompt' => 'Select..'])->label(false);
            ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
				<input type="email" id="email" placeholder="Email" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="password" class="col-sm-3 control-label">Password</label>
			<div class="col-sm-9">
				<input type="password" id="password" placeholder="Password" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="birthDate" class="col-sm-3 control-label">Date of Birth</label>
			<div class="col-sm-9">
				<input type="date" id="birthDate" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="country" class="col-sm-3 control-label">Country</label>
			<div class="col-sm-9">
				<select id="country" class="form-control">
					<option>Afghanistan</option>
					<option>Bahamas</option>
					<option>Cambodia</option>
					<option>Denmark</option>
					<option>Ecuador</option>
					<option>Fiji</option>
					<option>Gabon</option>
					<option>Haiti</option>
				</select>
			</div>
		</div> <!-- /.form-group -->
		<div class="form-group">
			<label class="control-label col-sm-3">Gender</label>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-4">
						<label class="radio-inline">
							<input type="radio" id="femaleRadio" value="Female">Female
						</label>
					</div>
					<div class="col-sm-4">
						<label class="radio-inline">
							<input type="radio" id="maleRadio" value="Male">Male
						</label>
					</div>
					<div class="col-sm-4">
						<label class="radio-inline">
							<input type="radio" id="uncknownRadio" value="Unknown">Unknown
						</label>
					</div>
				</div>
			</div>
		</div> <!-- /.form-group -->
		<div class="form-group">
			<label class="control-label col-sm-3">Meal Preference</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input type="checkbox" id="calorieCheckbox" value="Low calorie">Low calorie
					</label>
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" id="saltCheckbox" value="Low salt">Low salt
					</label>
				</div>
			</div>
		</div> <!-- /.form-group -->
		<div class="form-group">
			<div class="col-sm-9 col-sm-offset-3">
				<div class="checkbox">
					<label>
						<input type="checkbox">I accept <a href="#">terms</a>
					</label>
				</div>
			</div>
		</div> <!-- /.form-group -->
		<div class="form-group">
			<div class="col-sm-9 col-sm-offset-3">
				<button type="submit" class="btn btn-primary btn-block">Register</button>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
</div> <!-- ./container -->