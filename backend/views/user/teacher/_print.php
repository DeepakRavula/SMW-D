<?php

use kartik\grid\GridView;
use common\models\Lesson;
use common\models\Qualification;
?>
    <!-- title row -->
    <?php
   echo $this->render('/print/_print-header', [
       'userModel'=>$model,
       'locationModel'=>$model->userLocation->location,
]);
   ?>
      <!-- /.col -->
     <div class="col-sm-4 invoice-col">
        <b>Lessons</b><br>
        <br>
        <b><?php echo  $fromDate->format('F jS, Y') . ' to ' . $toDate->format('F jS, Y');?></b>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
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
    'summary' => '',
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
      <!-- /.col -->
    </div>
    <!-- /.row -->
	<div class="boxed col-md-12 pull-right">
<div class="sign">
 Teacher Signature <span></span>
</div>
<div class="sign">
Authorizing Signature <span></span>
</div>
<div class="sign">
 Date <span></span>
</div>
</div>
<script>
    $(document).ready(function () {
        window.print();
    });
</script>