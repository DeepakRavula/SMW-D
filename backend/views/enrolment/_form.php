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

<div class="enrolment-form form-well form-well-smw">
    <?php $form = ActiveForm::begin(); ?>
        <div class="col-md-12">
            <?= $form->errorSummary($model); ?>
        </div>
    <div class="row">
	<div class="col-md-4">
    <?php echo $form->field($model, 'programId')->dropDownList(ArrayHelper::map(Program::find()->active()->all(), 'id', 'name'),['prompt'=>'Select..']); ?>
	</div>
	<div class="col-md-4">
	<?php // Dependent Dropdown
	echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
		 'options' => ['id'=>'enrolment-teacherId'],
		 'pluginOptions'=>[
			 'depends'=>['enrolment-programid'],
			 'placeholder' => 'Select...',
			 'url' => Url::to(['/enrolment/teachers'])
		 ]
	 ]);?>
	 </div>
	 <div class="col-md-4">
	 	<?php // Dependent Dropdown
	echo $form->field($model, 'day')->widget(DepDrop::classname(), [
		 'options' => ['id'=>'teacher-availability-day'],
		 'pluginOptions'=>[
			 'depends'=>['enrolment-teacherId'],
			 'placeholder' => 'Select...',
			 'url' => Url::to(['/teacher-availability/available-days'])
		 ]
	 ]);?>
	 </div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<?php // Dependent Dropdown
	echo $form->field($model, 'fromTime')->widget(DepDrop::classname(), [
		 'options' => ['id'=>'fromTime-id'],
		 'pluginOptions'=>[
			 'depends'=>['enrolment-teacherId', 'teacher-availability-day'],
			 'placeholder' => 'Select...',
			 'url' => Url::to(['/teacher-availability/available-hours'])
		 ]
	 ]);?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'duration')->widget(TimePicker::classname(), [
	'pluginOptions' => [
		'showMeridian' => false,
		'defaultTime' => date('H:i',strtotime('00:45')),
	]
	]);?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'commencement_date')->widget(DatePicker::classname(),[
			'type' => DatePicker::TYPE_COMPONENT_APPEND,
			'pluginOptions' => [
    		    'format' => 'mm-dd-yy',
        		'todayHighlight' => true,
				'autoclose'=>true
    		]
			]);?>
		</div>
	</div>
    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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