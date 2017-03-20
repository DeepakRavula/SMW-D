<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use common\models\Course;
use common\models\Enrolment;
use common\models\TeacherAvailability;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
.smw-box {
    background: #f5ecec;
    border: 1px solid #f5ecec;
    padding: 10px;
    color: white;
    border-radius: 5px;
    color:#333;
}
.monthly-estimate {
	margin-top:20px;
	margin-bottom: 0px;
}
</style>
<?=
$this->render('_view-enrolment', [
	'model' => $model->enrolment,
]);
?>
<div>
<div class="smw-box col-md-10 m-l-10 m-b-10 monthly-estimate">
<?php
	$query = TeacherAvailability::find()
                ->joinWith('userLocation')
                ->where(['user_id' => $model->teacherId]);
        $teacherDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]); ?>
	<?= $this->render('_teacher-availability', [
    	'teacherDataProvider' => $teacherDataProvider,
	]); ?>
	</div>
	</div>
<div class="enrolment-form form-well form-well-smw">
		<?php $form			 = ActiveForm::begin(); ?>
    <div class="row">
		<?php
		$fromTime		 = Yii::$app->formatter->asTime($model->fromTime);
		$model->fromTime = !empty($model->fromTime) ? $fromTime : null;
		$model->paymentFrequency = $model->enrolment->paymentFrequency;
		?>
        <div class="col-md-4">
<?php echo $form->field($model, 'day')->dropdownList(Course::getWeekdaysList(), ['prompt' => 'select day']) ?>
        </div>
		<div class="col-md-4">
<?= $form->field($model, 'fromTime')->widget(TimePicker::classname(), []); ?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'rescheduleBeginDate')->widget(DatePicker::classname(),
				[
				'options' => [
					'value' => (new \DateTime())->format('d-m-Y'),
				],
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
				'pluginOptions' => [
					'autoclose' => true,
					'format' => 'dd-mm-yyyy'
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'endDate')->widget(DatePicker::classname(),
				[
				'options' => [
					'value' => (new \DateTime($model->endDate))->format('d-m-Y'),
				],
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
				'pluginOptions' => [
					'autoclose' => true,
					'format' => 'dd-mm-yyyy'
				]
			]);
			?>
		</div>
	</div>
    <div class="form-group col-md-4">
<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'),
	['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?= Html::a('Cancel', ['view', 'id' => $model->enrolment->id], ['class' => 'btn']);
        ?>
    </div>

<?php ActiveForm::end(); ?>

</div>