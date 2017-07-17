<?php

use common\models\Program;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
/* @var $form yii\bootstrap\ActiveForm */

?>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="group-course-form p-10">
	<?php
	$form = ActiveForm::begin([
			'id' => 'group-course-form',
	]);
	?>
	<div class="row p-10">
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'programId')->dropDownList(
				ArrayHelper::map(Program::find()->group()->active()
						->all(), 'id', 'name'), ['prompt' => 'Select Program'])->label('Program');
			?>
		</div>
		<div class="col-md-4">
			<?php
			// Dependent Dropdown
			echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
				'options' => ['id' => 'course-teacherid'],
				'pluginOptions' => [
					'depends' => ['course-programid'],
					'placeholder' => 'Select...',
					'url' => Url::to(['course/teachers']),
				],
			])->label('Teacher');
			?>
		</div>
		<div class="col-md-4">
			<?= $form->field($model, 'weeksCount')->textInput()->label('Number Of Weeks'); ?>
		</div>
		<div class="clearfix"></div>
		<div class="padding-v-md">
        <div class="line line-dashed"></div>
    </div>
        <div class="col-md-12">
		<?= $this->render('_form-add-lesson', [
			'form' => $form,
			'courseSchedule' => $courseSchedule
		]); ?>
        </div>
        <div class="col-md-12">
			<div class="form-group">
				<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['id' => 'group-course-save', 'class' => 'btn btn-primary', 'name' => 'signup-button'])
				?>
				<?php
				if (!$model->isNewRecord) {
					echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
				}
				?>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
	<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Choose Date, Day and Time</h4>',
		'id' => 'course-calendar-modal',
	]);
	?>
	<?php
	echo $this->render('_calendar', [
	]);
	?>
	<?php Modal::end(); ?>
