<?php

use common\models\Program;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="enrolment-form form-well form-well-smw">
	<?php $form = ActiveForm::begin(); ?>
	
    <div class="row">
		<div class="col-md-4">
			<?php echo $form->field($model, 'program_id')->dropDownList(ArrayHelper::map(Program::find()->active()->all(), 'id', 'name'), ['prompt' => 'Select..']); ?>
		</div>
		<div class="col-md-4">
			<?php
			// Dependent Dropdown
			echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
				'options' => ['id' => 'enrolment-teacherId'],
				'pluginOptions' => [
					'depends' => ['enrolment-program_id'],
					'placeholder' => 'Select...',
					'url' => Url::to(['/enrolment/teachers'])
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?php
			// Dependent Dropdown
			echo $form->field($model, 'day')->widget(DepDrop::classname(), [
				'options' => ['id' => 'teacher-availability-day'],
				'pluginOptions' => [
					'depends' => ['enrolment-teacherId'],
					'placeholder' => 'Select...',
					'url' => Url::to(['/teacher-availability/available-days'])
				]
			]);
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<?php
			// Dependent Dropdown
			echo $form->field($model, 'from_time')->widget(DepDrop::classname(), [
				'options' => ['id' => 'fromTime-id'],
				'pluginOptions' => [
					'depends' => ['enrolment-teacherId', 'teacher-availability-day'],
					'placeholder' => 'Select...',
					'url' => Url::to(['/teacher-availability/available-hours'])
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'duration')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'showMeridian' => false,
					'defaultTime' => date('H:i', strtotime('00:45')),
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?php 
            if($model->isNewRecord){
                $model->commencement_date = date('d-m-Y');
            }
            ?>
			<?php
			echo $form->field($model, 'commencement_date')->widget(DatePicker::classname(), [
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'options' => [
                    'value' => $model->commencement_date,                      
                    ],
       				'pluginOptions' => [
					'format' => 'dd-mm-yyyy',
					'todayHighlight' => true,
					'autoclose' => true
				]
			]);
			?>
		</div>
	</div>
    <div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>
