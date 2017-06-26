<?php

use kartik\grid\GridView;
?>
<?php $this->registerCssFile("@web/css/teacher/style.css");?>
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
			if( ! empty($data->invoice->date)) {
    			$invoiceDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->invoice->date);
			    return $invoiceDate->format('l, F jS, Y');
			}

			return null;
		},
		'contentOptions' => ['class' => 'text-left'],
		'group' => true,
		'groupedRow' => true,
		'groupFooter' => function ($model, $key, $index, $widget) {
			return [
				'mergeColumns' => [[1, 3]],
				'content' => [
					4 => GridView::F_SUM,
					5 => GridView::F_SUM,
				],
				'contentFormats' => [
					4 => ['format' => 'number', 'decimals' => 2],
					5 => ['format' => 'number', 'decimals' => 2],
				],
				'contentOptions' => [
					4 => ['style' => 'text-align:right'],
					5 => ['style' => 'text-align:right'],
				],
			'options'=>['style'=>'font-weight:bold;']
			];
		}
	],
		[
		'label' => 'Time',
		'width' => '250px',
		'value' => function ($data) {
			return !empty($data->lesson->date) ? Yii::$app->formatter->asTime($data->lesson->date) : null;
		},
	],
		[
		'label' => 'Program',
		'width' => '250px',
		'value' => function ($data) {
			return  !empty($data->lesson->enrolment->program->name) ? $data->lesson->enrolment->program->name : null;
		},
	],
		[
		'label' => 'Student',
		'value' => function ($data) {
			$student = ' - ';
			if($data->lesson->course->program->isPrivate()) {
				$student = !empty($data->lesson->enrolment->student->fullName) ? $data->lesson->enrolment->student->fullName : null;
			}
			return $student;
		},
	],
		[
		'label' => 'Duration(hrs)',
		'value' => function ($data) {
			return $data->unit;
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
			return $data->cost;
		},
		'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
	],
];
} else {
	$columns = [
		[
			'label' => 'Date',
			'value' => function ($data) {
				if( ! empty($data->invoice->date)) {
					$invoiceDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->invoice->date);
					return $invoiceDate->format('l, F jS, Y');
				}

				return null;
			},
		],	
		[
			'label' => 'Duration(hrs)',
			'value' => function ($data){
				return $data->getLessonDuration($data->invoice->date, $data->lesson->teacherId);
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
			return $data->getLessonCost($data->invoice->date, $data->lesson->teacherId);
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
	'dataProvider' => $timeVoucherDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'pjax' => true,
	'showPageSummary' => true,
	'pjaxSettings' => [
		'neverTimeout' => true,
		'options' => [
			'id' => 'time-voucher-grid',
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