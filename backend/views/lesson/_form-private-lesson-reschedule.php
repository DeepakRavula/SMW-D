<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-qualify p-10">

	<?=
		$this->render('view', [
    		'model' => $model,
    	]);
	?>
<?php $this->title = 'Missed Lesson';?>

<?php $form = ActiveForm::begin(); ?>
   <div class="row">
		<div class="col-xs-4">
			<?php
			    $date = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
			    $date->modify('90 days');
			    $expiryDate = $date->format('d-m-Y');?>
			
			<?= $form->field($privateLessonModel, 'isEligible')->checkbox();?>
			<?= $form->field($privateLessonModel, 'expiryDate')->widget(DateTimePicker::classname(), [
				'options' => [
					'value' => Yii::$app->formatter->asDateTime($expiryDate),
				],
				'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
				'pluginOptions' => [
					'autoclose' => true,
					'format' => 'dd-mm-yyyy HH:ii P'
				]
			]);
			?>
			<?php
			echo $form->field($model, 'date')->widget(DateTimePicker::classname(), [
				'options' => [
					'value' => Yii::$app->formatter->asDateTime($model->date),
				],
				'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
				'pluginOptions' => [
					'autoclose' => true,
					'format' => 'dd-mm-yyyy HH:ii P'
				]
			])->label('Reschedule Date');
			?>
			<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'button']) ?>
        </div>
		<div class="clearfix"></div>
	</div>
	<?php ActiveForm::end(); ?>
</div>