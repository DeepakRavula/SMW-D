<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="group-course-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>
<div class="row">
	<div class="col-md-4">
    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-md-4">
    <?php echo $form->field($model, 'rate')->textInput() ?>
	</div>
	<div class="col-md-4">
    <?php echo $form->field($model, 'length')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'showMeridian' => false,
					'defaultTime' => date('H:i', strtotime('00:30')),
				]
			]);?>

	</div>
</div>
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
