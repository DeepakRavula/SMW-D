<?php

use kartik\grid\GridView;
use common\models\Lesson;
use common\models\Qualification;
?>
<div class="row-fluid print-container">
	<div class="logo invoice-col">              
		<img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
	</div>
	<div class="location-address">
			<p>Arcadia Music Academy ( <?= $model->userLocation->location->name;?> )</p>
			<p><?php if (!empty($model->userLocation->location->address)): ?>
				<?= $model->userLocation->location->address ?><br>
			<?php endif; ?></p>
			<p><?php if (!empty($model->userLocation->location->city_id)): ?>
				<?= $model->userLocation->location->city->name ?>
			<?php endif; ?>
			<?php if (!empty($model->userLocation->location->province_id)): ?>
				<?= ', ' . $model->userLocation->location->province->name ?>
			<?php endif; ?> </p>
	</div>
	<div class="clearfix"></div>
</div>
<h2 class="col-md-12"><b><?= $model->publicIdentity . '\'s Lessons for ' . $fromDate->format('F jS, Y') . ' to ' . $toDate->format('F jS, Y');?></b></h2>
<div class="report-grid">
<?php
$columns = [
		[
		'value' => function ($data) {
			$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
			$date = $lessonDate->format('l, F jS, Y');
			return !empty($date) ? $date : null;
		},
		'contentOptions' => ['class' => 'text-left'],
		'group' => true,
		'groupedRow' => true,
		'groupFooter' => function ($model, $key, $index, $widget) {
			return [
				'mergeColumns' => [[1, 3]],
				'content' => [
					4 => GridView::F_SUM,
				],
				'contentFormats' => [
					4 => ['format' => 'number', 'decimals' => 2],
				],
				'contentOptions' => [
					4 => ['style' => 'text-align:right'],
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
];
?>
<?=
GridView::widget([
	'dataProvider' => $teacherLessonDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-responsive table-more-condensed'],
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
</div>
<script>
    $(document).ready(function () {
        window.print();
    });
</script>