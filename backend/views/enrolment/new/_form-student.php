<?php

use kartik\date\DatePicker;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'New Enrolment';
?>
<div class="row user-create-form">
	<div class="col-xs-5 pull-left">
        <label>First Name</label>
    </div>
			<div class="col-xs-7">
				<?php
            echo $form->field($model, 'first_name')->textInput(['placeholder' => 'First Name'])->label(false); ?>
			</div>
	<div class="col-xs-5 pull-left">
        <label>Last Name</label>
    </div>
			<div class="col-xs-7">
				<?php
            echo $form->field($model, 'last_name')->textInput(['placeholder' => 'Last Name'])->label(false); ?>
			</div>
   <div id="enrolment-student-spinner" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div> 
	<div class="col-xs-5 pull-left">
        <label>Birth Date</label>
    </div>
		<div class="col-xs-7">
		 <?php echo $form->field($model, 'birth_date')->textInput()->label(false)?>
        </div>
	<div class="clearfix"></div>
	<div class="pull-right">
		<?= Html::a('Cancel', '#', ['class' => 'm-r-10 btn btn-default new-enrol-cancel']); ?>
		 <?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['class' => 'btn btn-info', 'name' => 'signup-button', 'id' => 'new-enrol-save-btn']) ?>
	</div>
	<div class="form-group pull-left">
		<button class="step4-back btn btn-info" type="button" >Back</button>
    </div>
</div> <!-- ./container -->
<script>
$(document).ready(function() {
$.fn.datepicker.noConflict();
	$('#student-birth_date').datepicker({
	   altField: '#student-birth_date',
	   altFormat: 'dd-mm-yy',
	   changeMonth: true,
	   changeYear: true,
	   yearRange : '-70:today',
	   onChangeMonthYear:function(y, m, i){
		   var d = i.selectedDay;
		   $(this).datepicker('setDate', new Date(y, m-1, d));
	   }
	});
    $(document).on('click', '#new-enrol-save-btn', function() {
        $('#enrolment-student-spinner').show();
    });
        
});
</script>