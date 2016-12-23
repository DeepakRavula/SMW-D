<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use common\models\Course;
use common\models\Program;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="group-course-form p-10">

    <?php $form = ActiveForm::begin([
		'enableAjaxValidation' => true,
		'enableClientValidation' => false
	]); ?>

<div class="row">
	<div class="col-md-4">
    <?php echo $form->field($model, 'programId')->dropDownList(
                ArrayHelper::map(Program::find()->active()
                    ->where(['type' => Program::TYPE_GROUP_PROGRAM])
                    ->all(), 'id', 'name')) ?>
	</div>
	<div class="col-md-4">
            <?php echo $form->field($model, 'teacherId')->dropDownList($teacher) ?>
	</div>
	<div class="col-md-4">
    <?php echo $form->field($model, 'duration')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => date('H:i', strtotime('00:30')),
                ],
            ]); ?>

	</div>	
	</div>
	<div class="row">
		<div class="col-md-4">
            <?php echo $form->field($model, 'day')->dropdownList(Course::getWeekdaysList(), ['prompt' => 'select day']) ?>
		</div>
	<?php
        $fromTime = \DateTime::createFromFormat('H:i:s', $model->fromTime);
        $model->fromTime = !empty($model->fromTime) ? $fromTime->format('g:i A') : null;
    ?>
		<div class="col-md-4">
		<?= $form->field($model, 'fromTime')->widget(TimePicker::classname(), []); ?>
		</div>
		<div class="col-md-4">
            <?php echo $form->field($model, 'startDate')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class' => 'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '-70:today',
                    ],
                ]); ?>
		</div>
		</div>
		<div class="row">
		<div class="col-md-4">
            <?php echo $form->field($model, 'endDate')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class' => 'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '-70:today',
                    ],
                ]); ?>
		</div>
	</div>
	</div>
</div>
    <div class="form-group p-l-10">
       <?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
	<?php
        if (!$model->isNewRecord) {
            echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
        }
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
