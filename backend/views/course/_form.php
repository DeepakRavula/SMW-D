<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use common\models\Program;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Url;
use common\models\LocationAvailability;
use kartik\depdrop\DepDrop;
use kartik\time\TimePicker;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
/* @var $form yii\bootstrap\ActiveForm */
?>

<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="group-course-form p-10">
	<?php
	$form			 = ActiveForm::begin([
                        'id' => 'group-course-form',
	]);
	?>
		<div class="row p-10">
            <div class="col-md-4">
                    <?php
                    echo $form->field($model, 'programId')->dropDownList(
                            ArrayHelper::map(Program::find()->group()->active()
                               ->all(), 'id', 'name'), ['prompt' => 'Select Program'])->label('Program');?>
            </div>
            <div class="col-md-4">
				<?php
					// Dependent Dropdown
					echo $form->field($model, 'teacherId')->widget(DepDrop::classname(),
						[
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
				<?= $form->field($model, 'weeksCount')->textInput()->label('Number Of Weeks');?>
       		</div>
			<?php 
				$buttons = [
					[
						'label' => 'One',
						'options' => [
							'class' => 'btn btn-outline-info',
							'id' => 'lessons-per-week-one',
						],
					],

					[
						'label' => 'Two',
						'options' => [
							'class' => 'btn btn-outline-info',
							'id' => 'lessons-per-week-two',
						],
					],	
				]; 
			?>
			 <div class="col-md-4">
				<label>Number of Lessons Per Week</label>
				<br />
				<?php // a button group with items configuration
				echo ButtonGroup::widget([
					'buttons' => $buttons,
					'options' => [
						'id' => 'payment-method-btn-section',
						'class' => 'btn-group-horizontal m-0',
					],
				]); ?>
			</div>
           <div class="clearfix"></div>
       </div>
       	<div class="row">
		   <div class="col-md-12" id="lessonsPerWeekCountOne">
			   <div class="col-md-4">
	                     <?php
	                     echo $form->field($courseSchedule, 'duration')->widget(TimePicker::classname(),
	                 [
	                 'pluginOptions' => [
	                     'showMeridian' => false,
	                     'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
	                 ],
	             ]);
	             ?>
	            </div>
	            <div class="form-group">
	                <label>Choose date</label>

	                <div class="input-group">
	                  <button type="button" class="btn btn-default hand course-calendar-icon">
	                    <span>
	                      <i class="fa fa-calendar"></i> Date range picker
	                    </span>
	                    <i class="fa fa-caret-down"></i>
	                  </button>
	                </div>
	            </div>
			</div>
			<div class="col-md-12" id="lessonsPerWeekCountTwo">
				<div class="col-md-4">
					<?php
		                    echo $form->field($courseSchedule, 'duration')->widget(TimePicker::classname(),
		                [
		                'options' => ['id' => 'group-course-duration'],
		                'pluginOptions' => [
		                    'showMeridian' => false,
		                    'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
		                ],
		            ]);
		            ?>
		        </div>
		        <div class="form-group">
	                <label>Choose date</label>

	                <div class="input-group">
	                  <button type="button" class="btn btn-default hand group-course-calendar-icon">
	                    <span>
	                      <i class="fa fa-calendar"></i> Date range picker
	                    </span>
	                    <i class="fa fa-caret-down"></i>
	                  </button>
	                </div>
	            </div>
			</div>
		</div>
            <?= $form->field($courseSchedule, 'day[0]')->hiddenInput()->label(false); ?>
            <?= $form->field($courseSchedule, 'fromTime[0]')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'startDate[0]')->hiddenInput()->label(false); ?>
		    <?= $form->field($courseSchedule, 'duration[0]')->hiddenInput()->label(false); ?>
		    <?= $form->field($courseSchedule, 'day[1]')->hiddenInput()->label(false); ?>
            <?= $form->field($courseSchedule, 'fromTime[1]')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'startDate[1]')->hiddenInput()->label(false); ?>
		    <?= $form->field($courseSchedule, 'duration[1]')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'lessonsPerWeekCount')->hiddenInput()->label(false); ?>
        <div class="col-md-12">
		    <div class="form-group">
				<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'),
					['id' => 'group-course-save', 'class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
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
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Choose Date, Day and Time</h4>',
	'id' => 'group-course-calendar-modal',
]);
?>
<?php
echo $this->render('_group-calendar', [
]);
?>
<?php Modal::end(); ?>
<script>
	$(document).ready(function() {
		$('#lessonsPerWeekCountOne').hide();
		$('#lessonsPerWeekCountTwo').hide();
		$('#lessons-per-week-one').click(function() {
			$('#course-lessonsperweekcount').val(1);
			$('#lessonsPerWeekCountOne').show();
			$('#lessonsPerWeekCountTwo').hide();
			return false;
		});
		$('#lessons-per-week-two').click(function() {
			$('#course-lessonsperweekcount').val(2);
			$('#lessonsPerWeekCountOne').show();
			$('#lessonsPerWeekCountTwo').show();
			return false;
		});
	});
</script>
