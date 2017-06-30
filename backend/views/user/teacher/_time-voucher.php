<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
?>
<div class="col-md-12">
	<?php
	$form = ActiveForm::begin([
		'id' => 'time-voucher-search-form',
	]);
	?>
	<style>
		#w20-container table > tbody > tr.info > td{
			padding:8px;
			background:#fff;
		}
		.kv-page-summary, .table > tbody + tbody{
			border: 0;
		}
		.table-striped > tbody > tr:nth-of-type(odd){
			background: transparent;
		}
	</style>
	<div class="row">
		<div class="col-md-2">
			<?php
			echo $form->field($searchModel, 'fromDate')->widget(DatePicker::classname(), [
				'options' => [
					'class' => 'form-control',
				],
			])
			?>
		</div>
		<div class="col-md-2">
			<?php
			echo $form->field($searchModel, 'toDate')->widget(DatePicker::classname(), [
				'options' => [
					'class' => 'form-control',
				],
			])
			?>
		</div>
		<div class="col-md-2 form-group p-t-5">
			<Br>
			<?php echo Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'search', 'class' => 'btn btn-primary']) ?>
		</div>
		<div class="col-md-4 m-t-20">
			<div class="schedule-index">
			 <?= $form->field($searchModel, 'summariseReport')->checkbox(['data-pjax' => true]); ?>
        	</div>
		</div>
		<div class="col-md-2 m-t-25">
			<?= Html::a('<i class="fa fa-print"></i> Print', ['print-time-voucher', 'id' => $model->id], ['id' => 'time-voucher-print-btn', 'class' => 'btn btn-default btn-sm pull-right m-r-10', 'target' => '_blank']) ?>

		</div>
		<div class="clearfix"></div>
	</div>
</div>
<?php ActiveForm::end(); ?>

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
<script>
    $(document).ready(function () {
		$("#invoicesearch-summarisereport").on("change", function() {
        var summariesOnly = $(this).is(":checked");
        var fromDate = $('#invoicesearch-fromdate').val();
        var toDate = $('#invoicesearch-todate').val();
        var params = $.param({ 'InvoiceSearch[fromDate]': fromDate,
            'InvoiceSearch[toDate]': toDate, 'InvoiceSearch[summariseReport]': (summariesOnly | 0) });
        var url = '<?php echo Url::to(['user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $model->id]); ?>&' + params;
        $.pjax.reload({url:url,container:"#time-voucher-grid",replace:false,  timeout: 4000});  //Reload GridView
		var printUrl = '<?= Url::to(['user/print-time-voucher', 'id' => $model->id]); ?>&' + params;
		 $('#time-voucher-print-btn').attr('href', printUrl);
    });
        $("#time-voucher-search-form").on("submit", function () {
        	var summariesOnly = $("#invoicesearch-summarisereport").is(":checked");
            var fromDate = $('#invoicesearch-fromdate').val();
            var toDate = $('#invoicesearch-todate').val();
            $.pjax.reload({container: "#time-voucher-grid", replace: false, timeout: 6000, data: $(this).serialize()});
			var params = $.param({ 'InvoiceSearch[fromDate]': fromDate,
            'InvoiceSearch[toDate]': toDate, 'InvoiceSearch[summariseReport]': (summariesOnly | 0) });
            var url = '<?= Url::to(['user/print-time-voucher', 'id' => $model->id]); ?>&' + params;
            $('#time-voucher-print-btn').attr('href', url);
            return false;
        });
    });
</script>