<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use common\models\Lesson;

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
				if($privateLessonModel->isNewRecord){
					$date = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
					$date->modify('90 days');
					$privateLessonModel->expiryDate = $date->format('d-m-Y H:i:s');
				}
			?>
			
			<?= $form->field($privateLessonModel, 'isEligible')->checkbox()->label('Not Qualify for Reschedule');?>
			<?= $form->field($privateLessonModel, 'expiryDate')->widget(DateTimePicker::classname(), [
				'options' => [
					'value' => Yii::$app->formatter->asDateTime($privateLessonModel->expiryDate),
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
					'value' => $model->status === Lesson::STATUS_CANCELED ? '' : Yii::$app->formatter->asDateTime($model->date),
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
<script>
	$(document).ready(function(){
    $('#privatelesson-iseligible').change(function(){
        if($(this).is(':checked')) {
            $('#lesson-date').prop('disabled', true);
            $('#privatelesson-expirydate').prop('disabled', true);
        } else {
            $('#lesson-date').prop('disabled', false);
            $('#privatelesson-expirydate').prop('disabled', false);
        }
    });
});
</script>