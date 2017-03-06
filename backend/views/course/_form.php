<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use common\models\Course;
use common\models\Program;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\TeacherAvailability;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="group-course-form p-10">
	<?php
	$form			 = ActiveForm::begin([
			'enableAjaxValidation' => true,
			'enableClientValidation' => false
	]);
	?>
	<?php
	$query = TeacherAvailability::find()
                ->joinWith('userLocation')
                ->where(['user_id' => key($teacher)]);
        $teacherDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]); ?>
	<div class="row p-10">
				<div class="col-md-6">
					<?php
					echo $form->field($model, 'programId')->dropDownList(
						ArrayHelper::map(Program::find()->active()
								->where(['type' => Program::TYPE_GROUP_PROGRAM])
								->all(), 'id', 'name'))
					?>
				</div>
				<div class="col-md-6">
					<?php echo $form->field($model, 'teacherId')->dropDownList($teacher) ?>
				</div>
				<div class="col-md-6">
					<?php
					echo $form->field($model, 'duration')->widget(TimePicker::classname(),
						[
						'pluginOptions' => [
							'showMeridian' => false,
							'defaultTime' => date('H:i', strtotime('00:30')),
						],
					]);
					?>
				</div>
				<div class="col-md-6">
					<?php echo $form->field($model, 'day')->dropdownList(Course::getWeekdaysList(), ['prompt' => 'select day']) ?>
				</div>
				<div class="col-md-6">
					<?php
					$fromTime		 = \DateTime::createFromFormat('H:i:s', $model->fromTime);
					$model->fromTime = !empty($model->fromTime) ? $fromTime->format('g:i A') : null;
					?>

					<?= $form->field($model, 'fromTime')->widget(TimePicker::classname(), []); ?>
				</div>
				<div class="col-md-6">
					<?php
					echo $form->field($model, 'startDate')->widget(\yii\jui\DatePicker::classname(),
						[
						'options' => ['class' => 'form-control'],
						'clientOptions' => [
							'changeMonth' => true,
							'changeYear' => true,
						],
					]);
					?>
				</div>
				<div class="col-md-6">
					<?php
					echo $form->field($model, 'endDate')->widget(\yii\jui\DatePicker::classname(),
						[
						'options' => ['class' => 'form-control'],
						'clientOptions' => [
							'changeMonth' => true,
							'changeYear' => true,
						],
					]);
					?>
				</div>
		</div>
		<div class="col-md-11 teacher-availability">
			<?php echo $this->render('_teacher-availability', [
        		'teacherDataProvider' => $teacherDataProvider,
    		]); ?>
		</div>
    <div class="form-group p-l-10">
<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'),
	['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
<?php
if (!$model->isNewRecord) {
	echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
}
?>
    </div>
<?php ActiveForm::end(); ?>
</div>
<script>
	function fetchTeacherAvailability(teacherId) {
		$.ajax({
			url: '<?= Url::to(['course/fetch-teacher-availability']); ?>' + '?teacherId=' + teacherId,
			type: 'get',
			dataType: "json",
			success: function (response)
			{
				console.log(response.data);
				$('.teacher-availability').html(response);
			}
		});
	}
$(document).ready(function(){
	$('.teacher-availability').show();
	$(document).on('change', '#course-teacherid', function(){
		var teacherId = $('#course-teacherid').val();
		fetchTeacherAvailability(teacherId)
	});
});
</script>