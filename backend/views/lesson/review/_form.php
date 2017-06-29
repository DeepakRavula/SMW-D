<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;


/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php $form = ActiveForm::begin([
	'id' => 'lesson-review-form',
	'enableAjaxValidation' => true,
	'enableClientValidation' => false
]);
?>
<div class="row">
	<div class="col-md-6">
			<?php
			echo $form->field($model, 'date')->widget(DateTimePicker::classname(), [
				'options' => [
					'value' => $model->isUnscheduled() ? '' : Yii::$app->formatter->asDateTime($model->date),
				],
				'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
				'pluginOptions' => [
					'autoclose' => true,
					'format' => 'dd-mm-yyyy HH:ii P',
					'showMeridian' => true,
					'minuteStep' => 15,
				],
			])->label('Date & Time');
			?>
		</div>
	<div class="col-md-4">
		<?php
		echo $form->field($model, 'duration')->widget(TimePicker::classname(), [
			'options' => ['id' => 'course-duration'],
			'pluginOptions' => [
				'showMeridian' => false,
			],
		]);
		?>
	</div>
	<?= $form->field($model, 'id')->hiddenInput()->label(false);?>
	<div class="col-md-12 p-l-20 form-group">
		<?= Html::submitButton(Yii::t('backend', 'Apply'), ['id' => 'lesson-review-apply', 'class' => 'btn btn-primary', 'name' => 'button']) ?>
		<?= Html::a('Cancel','#', ['id' => 'lesson-review-cancel','class' => 'btn btn-default']);
		?>
		<div class="clearfix"></div>
	</div>
</div>
<?php ActiveForm::end(); ?>