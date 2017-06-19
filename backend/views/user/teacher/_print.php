<?php

use kartik\grid\GridView;
use common\models\Lesson;
use common\models\Qualification;
?>
<style>
@media print{
	.print-container{
		margin-top:5px;
	}
	.text-left{
        text-align: left !important;
    }
	.location-address {
	  text-align : right;
	  font-size : 18px;
	}
	.location-address p{
		margin:0;
		padding:0;
		font-weight:normal;
	}
	.boxed {
	  border: 4px solid #949599;
	  height: 200px;
	  margin: 20px;
	  padding: 20px;
	  width: 580px;
	}
	.sign {
	  font-weight: bold;
	  text-align : right;
	  font-size : 28px;
	}
	.sign span {
	  width: 250px;
	  display: inline-block;
	  border-bottom: 1px solid #999999;
	  font-weight: normal;
	}
	.login-logo-img {
		width:300px !important;
		height:auto;
	}
	.report-grid #teacher-lesson-grid table thead{
		border-bottom: 1px ridge;
	}
	.report-grid #teacher-lesson-grid table tbody tr.kv-grid-group-row{
		border-bottom: 1px ridge;
	}
	.report-grid #teacher-lesson-grid table tbody tr.kv-group-footer{
		border-top: 1px ridge;
	}
	.report-grid .table-bordered{
		border: 1px solid transparent;
	}
	.report-grid .table-bordered>thead>tr>th, .report-grid .table-bordered>tbody>tr>th,.report-grid  .table-bordered>tfoot>tr>th,.report-grid  .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .report-grid .table-bordered>tfoot>tr>td{
		border:none !important;
	}
	.report-grid .table-bordered > tbody > tr:nth-child(even){
		
	}
}
@page{
  size: auto;
  margin: 3mm;
}
</style>
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
<h2 class="col-md-12"><b><?= $model->publicIdentity . '\'s Time Voucher for ' . $fromDate->format('F jS, Y') . ' to ' . $toDate->format('F jS, Y');?></b></h2>
<div class="report-grid">
<?php
if(!$searchModel->summariseReport) {
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
		'format'=>['decimal',2],
		'value' => function ($data) {
			$qualification = Qualification::findone(['teacher_id' => $data->teacherId, 'program_id' => $data->course->program->id]);
			$rate = !empty($qualification->rate) ? $qualification->rate : 0;
			return $data->getDuration() * $rate;
		},
		'hAlign' => 'right',
		'contentOptions' => ['class' => 'text-right'],
	],
	[
		'label' => 'Cost',
		'format' => ['decimal', 2],
		'value' => function ($data) {
			$qualification = Qualification::findone(['teacher_id' => $data->teacherId, 'program_id' => $data->course->program->id]);
			$rate = !empty($qualification->rate) ? $qualification->rate : 0;
			return $data->getDuration() * $rate;
		},
		'contentOptions' => ['class' => 'text-right'],
		'hAlign' => 'right',
		'pageSummary' => true,
		'pageSummaryFunc' => GridView::F_SUM
	],
];
} else {
	$columns = [
		[
			'label' => 'Date',
			'value' => function ($data) {
				if( ! empty($data->date)) {
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					return $lessonDate->format('l, F jS, Y');
				}

				return null;
			},
		],	
		[
			'label' => 'Duration(hrs)',
			'value' => function ($data){
				$locationId = Yii::$app->session->get('location_id');
				$lessons = Lesson::find()
					->location($locationId)
					->notDeleted()
					->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED, Lesson::STATUS_SCHEDULED]])
					->andWhere(['DATE(date)' => (new \DateTime($data->date))->format('Y-m-d'), 'lesson.teacherId' => $data->teacherId])
					->all();
				$totalDuration = 0;
				foreach($lessons as $lesson) {
					$duration		 = \DateTime::createFromFormat('H:i:s', $lesson->fullDuration);
					$hours			 = $duration->format('H');
					$minutes		 = $duration->format('i');
					$lessonDuration	 = $hours + ($minutes / 60);
					$totalDuration += $lessonDuration;	
				}
				return $totalDuration;
			},
			'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
		],
		[
			'label' => 'Cost',
		'format'=>['decimal',2],
		'value' => function ($data) {
				$locationId = Yii::$app->session->get('location_id');
				$lessons = Lesson::find()
					->location($locationId)
					->notDeleted()
					->andWhere(['DATE(date)' => (new \DateTime($data->date))->format('Y-m-d'), 'lesson.teacherId' => $data->teacherId])
					->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED, Lesson::STATUS_SCHEDULED]])
					->all();
				$cost = 0;
				foreach($lessons as $lesson) {
					$qualification = Qualification::findone(['teacher_id' => $lesson->teacherId, 'program_id' => $lesson->course->program->id]);
					$rate = !empty($qualification->rate) ? $qualification->rate : 0;
					$duration		 = \DateTime::createFromFormat('H:i:s', $lesson->fullDuration);
					$hours			 = $duration->format('H');
					$minutes		 = $duration->format('i');
					$lessonDuration	 = $hours + ($minutes / 60);
					$cost += $lessonDuration * $rate;	
				}
				return $cost;
		},
		'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
	],
	];
}
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