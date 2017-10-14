<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
?>
<div class="col-md-12">
    <?php
    $form = ActiveForm::begin([
            'id' => 'time-voucher-search-form',
    ]);
    ?>
    <div class="clearfix">
    </div>
    <div class="row">
        <div class="col-md-3 form-group">
            <?php
            echo DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'dateRange',
                'convertFormat' => true,
                'initRangeExpr' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'ranges' => [
                        Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                        Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                    ],
                    'locale' => [
                        'format' => 'M d,Y',
                    ],
                    'opens' => 'right',
                ],
            ]);

            ?>
        </div>
        <div class="col-md-1 form-group">
<?php echo Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'search', 'class' => 'btn btn-primary']) ?>
        </div>
        <div class="col-md-1 form-group">
<?= Html::a('<i class="fa fa-print"></i> Print', ['print/time-voucher', 'id' => $model->id], ['id' => 'time-voucher-print-btn', 'class' => 'btn btn-default m-r-10', 'target' => '_blank']) ?>

        </div>
        <div class="pull-right">
<?= $form->field($searchModel, 'summariseReport')->checkbox(['data-pjax' => true]); ?>
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
			'label' => 'Rate/hr',
			'value' => function ($data) {
				return !empty($data->rate) ? $data->rate : 0;
			},
			'contentOptions' => ['class' => 'text-right'],
			'hAlign' => 'right',
			'format'=>['decimal',2],
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
		var printUrl = '<?= Url::to(['print/time-voucher', 'id' => $model->id]); ?>&' + params;
		 $('#time-voucher-print-btn').attr('href', printUrl);
    });
        $("#time-voucher-search-form").on("submit", function () {
        	var summariesOnly = $("#invoicesearch-summarisereport").is(":checked");
            var fromDate = $('#invoicesearch-fromdate').val();
            var toDate = $('#invoicesearch-todate').val();
            $.pjax.reload({container: "#time-voucher-grid", replace: false, timeout: 6000, data: $(this).serialize()});
			var params = $.param({ 'InvoiceSearch[fromDate]': fromDate,
            'InvoiceSearch[toDate]': toDate, 'InvoiceSearch[summariseReport]': (summariesOnly | 0) });
            var url = '<?= Url::to(['print/time-voucher', 'id' => $model->id]); ?>&' + params;
            $('#time-voucher-print-btn').attr('href', url);
            return false;
        });
    });
</script>