<?php

use common\models\User;
use common\models\Student;
use common\models\Program;
use common\models\Enrolment;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use yii\jui\DatePicker;
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


<<<<<<< HEAD
    <?php echo $form->field($model, 'day')->dropdownList(Enrolment::getWeekdaysList());?>
	
    <?php echo $form->field($model, 'fromTime'); ?>
    
=======
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
		 'options' => ['id'=>'day-id'],
		 'pluginOptions'=>[
			 'depends'=>['enrolment-teacherId'],
			 'placeholder' => 'Select...',
			 'url' => Url::to(['/teacher-availability/days'])
		 ]
	 ]);?>

		<?php // Dependent Dropdown
	echo $form->field($model, 'fromTime')->widget(DepDrop::classname(), [
		 'options' => ['id'=>'fromTime-id'],
		 'pluginOptions'=>[
			 'depends'=>['enrolment-teacherId'],
			 'placeholder' => 'Select...',
			 'url' => Url::to(['/teacher-availability/fromtimes'])
		 ]
	 ]);?>

    <?php echo $form->field($model, 'duration')->dropdownList(Enrolment::getDuration());?>

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