<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
?>
<?php
$totalDuration	 = 0;
$count			 = $teacherLessonDataProvider->getCount();
if (!empty($teacherLessonDataProvider->getModels())) {
	foreach ($teacherLessonDataProvider->getModels() as $key => $val) {
		$duration		 = \DateTime::createFromFormat('H:i:s', $val->duration);
		$hours			 = $duration->format('H');
		$minutes		 = $duration->format('i');
		$lessonDuration	 = ($hours * 60) + $minutes;
		$totalDuration += $lessonDuration;
	}
}
?>
<div>
  <?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default btn-sm pull-right m-r-10', 'target' => '_blank']) ?>
	<?php $form = ActiveForm::begin([
	'id' => 'teacher-lesson-search-form',
]);
?>
	<div class="col-md-3">
		<?php
		echo $form->field($searchModel, 'fromDate')->widget(DatePicker::classname(),
			[
			'options' => [
				'class' => 'form-control',
			],
		])
		?>
    </div>
    <div class="col-md-3">
		<?php
		echo $form->field($searchModel, 'toDate')->widget(DatePicker::classname(),
			[
			'options' => [
				'class' => 'form-control',
			],
		])
		?>
    </div>
    <div class="col-md-3 form-group m-t-5">
        <br>
	<?php echo Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'search', 'class' => 'btn btn-primary']) ?>
    </div>
	<?php ActiveForm::end(); ?>
	<?php
	yii\widgets\Pjax::begin([
		'id' => 'teacher-lesson-grid',
	])
	?>
	<?php
	echo GridView::widget([
		'id' => 'teacher-lesson',
		'dataProvider' => $teacherLessonDataProvider,
		'options' => ['class' => 'col-md-12'],
		'footerRowOptions' => ['style' => 'font-weight:bold;text-align: left;'],
		'showFooter' => true,
		'tableOptions' => ['class' => 'table table-bordered'],
		'headerRowOptions' => ['class' => 'bg-light-gray'],
		'columns' => [
			[
				'label' => 'Time',
				'value' => function ($data) {
					return !empty($data->date) ? Yii::$app->formatter->asTime($data->date) : null;
				},
				'footer' => 'Total Hours of Instruction',
			],
			[
				'label' => 'Program Name',
				'value' => function ($data) {
					return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
				},
			],
			[
				'label' => 'Student Name',
				'value' => function ($data) {
					return !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
				},
			],
			[
				'label' => 'Duration',
				'value' => function ($data) {
					$duration		 = \DateTime::createFromFormat('H:i:s', $data->duration);
					$hours			 = $duration->format('H');
					$minutes		 = $duration->format('i');
					$lessonDuration	 = ($hours * 60) + $minutes;

					return $lessonDuration.'m';
				},
				'headerOptions' => ['class' => 'text-right'],
				'contentOptions' => ['class' => 'text-right'],
				'footer' => $totalDuration.'m',
			],
		],
	]);
	?>
	<?php \yii\widgets\Pjax::end(); ?>
</div>
<script>
$(document).ready(function(){
	$('#teacher-lesson-search-form').submit(function(){
		var url = "<?= Url::to(['user/lesson-search', 'id' => $model->id]); ?>";
		$.pjax.reload({url:url, container:"#teacher-lesson-grid", replace: false, timeout : 6000, data : $(this).serialize()});  //Reload GridView
		 return false;
	});
});
</script>