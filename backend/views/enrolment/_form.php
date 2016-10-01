<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?= $this->render('_view-enrolment',[
	'model' => $model,
]);?>
<div class="enrolment-form form-well form-well-smw">
	<?php $form = ActiveForm::begin(); ?>
    <div class="row">
		<?php
			$fromTime = Yii::$app->formatter->asTime($model->course->fromTime);
			$model->fromTime = ! empty($model->course->fromTime) ? $fromTime : null;
		?>
		<div class="col-md-4">
			<?= $form->field($model,'fromTime')->widget(TimePicker::classname(), []); ?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'rescheduleBeginingDate')->widget(DatePicker::classname(), [
               		'options' => [
                    'value' => (new \DateTime())->format('d-m-Y'),
               ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
			  ])->label('Reschedule Future Lessons From'); ?>
		</div>
	</div>
    <div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>
