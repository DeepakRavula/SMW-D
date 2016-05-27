<?php

use common\models\User;
use common\models\Student;
use common\models\Program;
use common\models\Enrolment;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="enrolment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'programId')->dropDownList(ArrayHelper::map(Program::find()->active()->all(), 'id', 'name'),['prompt'=>'Select..']); ?>


	<?php // Dependent Dropdown
	echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
		 'options' => ['id'=>'enrolment-teacherId'],
		 'pluginOptions'=>[
			 'depends'=>['enrolment-programid'],
			 'placeholder' => 'Select...',
			 'url' => Url::to(['/enrolment/teachers'])
		 ]
	 ]);?>
		<?php // Dependent Dropdown
	echo $form->field($model, 'day')->widget(DepDrop::classname(), [
		 'options' => ['id'=>'teacher-availability-day'],
		 'pluginOptions'=>[
			 'depends'=>['enrolment-teacherId'],
			 'placeholder' => 'Select...',
			 'url' => Url::to(['/teacher-availability/available-days'])
		 ]
	 ]);?>

		<?php // Dependent Dropdown
	echo $form->field($model, 'fromTime')->widget(DepDrop::classname(), [
		 'options' => ['id'=>'fromTime-id'],
		 'pluginOptions'=>[
			 'depends'=>['enrolment-teacherId', 'teacher-availability-day'],
			 'placeholder' => 'Select...',
			 'url' => Url::to(['/teacher-availability/available-hours'])
		 ]
	 ]);?>

    <?php echo $form->field($model, 'duration')->widget(TimePicker::classname(), [
	'pluginOptions' => [
		'showMeridian' => false,
		'defaultTime' => date('H:i',strtotime('00:45')),
	]
	]);?>

    <?php echo $form->field($model, 'commencement_date')->widget(DatePicker::classname());?>

    <?php echo $form->field($model, 'renewal_date')->widget(DatePicker::classname());?>
	
    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
$('#enrolment-fromtime').timepicker({
    'minTime': '9:00am',
    'maxTime': '8:30pm',
	'step' : 30,
    'showDuration': false
});
</script>