<?php

use common\models\Program;
use common\models\Enrolment;
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
			<?php echo $form->field($model, 'programId')->dropDownList(
					ArrayHelper::map(Program::find()
						->active()
						->where(['type' => Program::TYPE_PRIVATE_PROGRAM])
						->all(), 
					'id', 'name'), ['prompt' => 'Select..']); ?>
		</div>
		<div class="col-md-4">
			<?php
			// Dependent Dropdown
			echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
				'options' => ['id' => 'course-teacherid'],
				'pluginOptions' => [
					'depends' => ['course-programid'],
					'placeholder' => 'Select...',
					'url' => Url::to(['/course/teachers'])
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
					'depends' => ['course-teacherid'],
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
			echo $form->field($model, 'fromTime')->widget(DepDrop::classname(), [
				'options' => ['id' => 'fromTime-id'],
				'pluginOptions' => [
					'depends' => ['course-teacherid', 'teacher-availability-day'],
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
                $model->startDate = (new \DateTime())->format('d-m-Y');
            }
            ?>
			<?php
			echo $form->field($model, 'startDate')->widget(DatePicker::classname(), [
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'options' => [
                    'value' => $model->startDate,                      
                    ],
       				'pluginOptions' => [
					'format' => 'dd-mm-yyyy',
					'todayHighlight' => true,
					'autoclose' => true
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<strong>Payment Frequency</strong>
        	<?= $form->field($model, 'paymentFrequency')->radio(['value' => Enrolment::TYPE_MONTHLY])->label('Monthly');?>
        	<?= $form->field($model, 'paymentFrequency')->radio(['value' => Enrolment::TYPE_FULL])->label('Full');?>
		</div>
	</div>
    <div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>
