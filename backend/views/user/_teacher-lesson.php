<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
?>
<?php
die('sdfjdh');
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
</div>
