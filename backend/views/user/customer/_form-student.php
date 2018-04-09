<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Student;
use yii\helpers\Url;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="row user-create-form">
	<?php
    $session = Yii::$app->session;
    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
    ?>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['student/create', 'userId' => $customer->id]),
        'id' => 'modal-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validationUrl' => Url::to(['student/validate']),
        ]); ?>

    <div class="row">
		<?php echo $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
		 <?php
        $customerName = $model->isNewRecord ? $customer->userProfile->lastname : null;
    ?>
		<?php echo $form->field($model, 'last_name')->textInput(['maxlength' => true, 'value' => $customerName]) ?>
		<?php echo $form->field($model, 'birth_date')->widget(DatePicker::classname(),
					[
					'type' => DatePicker::TYPE_INPUT,
					'pluginOptions' => [
						'autoclose' => true,
						'format' => 'M dd,yyyy'
					]
				]);?>
        </div>
	<?php echo $form->field($customer, 'id')->hiddenInput()->label(false); ?>

    <?php ActiveForm::end(); ?>
</div>

<script>
$(document).ready(function() {
$.fn.datepicker.noConflict();
	$('#student-birth_date').datepicker({
	   altField: '#student-birth_date',
	   altFormat: 'M d,yy',
	   changeMonth: true,
	   changeYear: true,
	   yearRange : '-70:today',
	   onChangeMonthYear:function(y, m, i){
		   var d = i.selectedDay;
		   $(this).datepicker('setDate', new Date(y, m-1, d));
	   }
	});
});
</script>