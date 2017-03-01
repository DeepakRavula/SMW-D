<?php

use kartik\grid\GridView;
?>
<h3><?= $model->publicIdentity; ?></h3>
<h4><?= $fromDate->format('l, jS Y') . ' to ' . $toDate->format('l, jS Y'); ?></h4>
<?php
$columns = [
		[
		'value' => function ($data) {
			$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
			$date = $lessonDate->format('l, F jS, Y');
			return !empty($date) ? $date : null;
		},
		'group' => true,
		'groupedRow' => true,
		'groupFooter' => function ($model, $key, $index, $widget) {
			return [
				'mergeColumns' => [[1, 3]],
				'content' => [
					4 => GridView::F_SUM,
					6 => GridView::F_SUM,
				],
				'contentFormats' => [
					4 => ['format' => 'number', 'decimals' => 2],
					6 => ['format' => 'number', 'decimals' => 2],
				],
				'contentOptions' => [
					4 => ['style' => 'text-align:right'],
					6 => ['style' => 'text-align:right'],
				],
				'options' => ['style' => 'font-weight:bold;']
			];
		}
	],
		[
		'label' => 'Time',
		'width' => '250px',
		'value' => function ($data) {
			return !empty($data->date) ? Yii::$app->formatter->asTime($data->date) : null;
		},
	],
		[
		'label' => 'Program',
		'width' => '250px',
		'value' => function ($data) {
			return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
		},
	],
		[
		'label' => 'Student',
		'value' => function ($data) {
			$student = ' - ';
			if($data->course->program->isPrivate()) {
				$student = !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
			}
			return $student;
		},
	],
		[
		'label' => 'Duration(hrs)',
		'value' => function ($data) {
			return $data->getDuration();
		},
		'contentOptions' => ['class' => 'text-right'],
		'hAlign' => 'right',
		'pageSummary' => true,
		'pageSummaryFunc' => GridView::F_SUM
	],
	[
	'label' => 'Rate/hour',
	'value' => function ($data) {
		return !empty($data->teacher->teacherRate->hourlyRate) ? $data->teacher->teacherRate->hourlyRate : null;
	},
	'hAlign' => 'right',
	'contentOptions' => ['class' => 'text-right'],
	],
	[
		'label' => 'Cost',
		'format' => ['decimal', 2],
		'value' => function ($data) {
			$teacherRate = !empty($data->teacher->teacherRate->hourlyRate) ? $data->teacher->teacherRate->hourlyRate : null;
			return $data->getDuration() * $teacherRate;
		},
		'contentOptions' => ['class' => 'text-right'],
		'hAlign' => 'right',
		'pageSummary' => true,
		'pageSummaryFunc' => GridView::F_SUM
	],
];
?>
<?=
GridView::widget([
	'dataProvider' => $teacherLessonDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-responsive'],
	'headerRowOptions' => ['class' => 'bg-light-gray-1'],
	'pjax' => true,
	'showPageSummary' => true,
	'pjaxSettings' => [
		'neverTimeout' => true,
		'options' => [
			'id' => 'teacher-lesson-grid',
		],
	],
	'columns' => $columns,
]);
?>
<script>
    $(document).ready(function () {
        window.print();
    });
</script>