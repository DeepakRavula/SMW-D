<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use common\models\Course;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?= $this->render('_view-enrolment',[
	    'model' => $model->enrolment,
]);?>
<div class="enrolment-form form-well form-well-smw">
	<?php $form = ActiveForm::begin(); ?>
    <div class="row">
		<?php
			$fromTime = Yii::$app->formatter->asTime($model->fromTime);
            $model->fromTime = ! empty($model->fromTime) ? $fromTime : null;

		?>
        <div class="col-md-4">
		    <?php echo $form->field($model, 'day')->dropdownList(Course::getWeekdaysList(),['prompt' => 'select day']) ?>
        </div>
		<div class="col-md-4">
			<?= $form->field($model,'fromTime')->widget(TimePicker::classname(), []); ?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'rescheduleBeginDate')->widget(DatePicker::classname(), [
               		'options' => [
                    'value' => (new \DateTime())->format('d-m-Y'),
               ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
			  ]); ?>
		</div>
	</div>
    <div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>
