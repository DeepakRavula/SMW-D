<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use common\models\GroupCourse;
use common\models\Program;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="group-course-form">

    <?php $form = ActiveForm::begin(); ?>

<div class="row">
	<div class="col-md-4">
    <?php echo $form->field($model, 'program_id')->dropDownList(
				ArrayHelper::map(Program::find()->active()
					->where(['type' => Program::TYPE_GROUP_PROGRAM])
					->all(), 'id', 'name')) ?>
	</div>
	<div class="col-md-4">
    <?php echo $form->field($model, 'length')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'showMeridian' => false,
					'defaultTime' => date('H:i', strtotime('00:30')),
				]
			]);?>

	</div>
	<div class="col-md-4">
            <?php echo $form->field($model, 'teacher_id')->dropDownList($teacher) ?>
	</div>
	</div>
	<div class="row">
	    
		<div class="col-md-4">
            <?php echo $form->field($model, 'day')->dropdownList(GroupCourse::getWeekdaysList(),['prompt' => 'select day']) ?>
		</div>
	<?php
		$fromTime = \DateTime::createFromFormat('H:i:s', $model->from_time);
		$model->from_time = ! empty($model->from_time) ? $fromTime->format('g:i A') : null;
	?>
		<div class="col-md-4">
		<?= $form->field($model, 'from_time')->widget(TimePicker::classname(), []); ?>
		</div>
		<div class="col-md-4">
            <?php echo $form->field($model, 'start_date')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class'=>'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '-70:today' 
                    ]
                ]); ?>
		</div>
		</div>
		<div class="row">
		<div class="col-md-4">
            <?php echo $form->field($model, 'end_date')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class'=>'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '-70:today' 
                    ]
                ]); ?>
		</div>
	</div>
	</div>
</div>
    <div class="form-group p-l-10">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
	<?php
        if (!$model->isNewRecord) {
            echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
        }
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
