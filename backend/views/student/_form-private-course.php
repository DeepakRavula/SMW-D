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
			<?php
			echo $form->field($model, 'duration')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'showMeridian' => false,
					'defaultTime' => date('H:i', strtotime('00:30')),
				]
			]);
			?>
		</div>
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
					'url' => Url::to(['/course/teachers']),
				]
			]);
			?>
		</div>
	</div>
	<div id="course-detail" class="row">
		<div class="col-md-4">
        	<?= $form->field($model, 'day')->hiddenInput()->label(false)?>
		</div>
		<div class="col-md-4">
        	<?= $form->field($model, 'fromTime')->hiddenInput()->label(false)?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'startDate')->widget(DatePicker::classname(), [
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
       				'pluginOptions' => [
					'format' => 'dd-mm-yyyy',
					'todayHighlight' => true,
					'autoclose' => true
				]
			])->hiddenInput()->label(false);
			?>
		</div>
	</div>
	<div id="calendar" class="row">
    <?php echo $this->render('_calendar', [
		'model' =>  $model,
    ]) ?>
	</div>
	<div class="row">
		<div class="col-md-4">
        	<?= $form->field($model, 'paymentFrequency')->radioList(Enrolment::paymentFrequencies())?>
		</div>
	</div>
    <div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
<div class="clearfix"></div>
	<?php ActiveForm::end(); ?>

</div>
