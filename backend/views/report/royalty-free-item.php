<?php

use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Royalty Free Items';
?>
<div class="form-group form-inline">
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>

<div class="clearfix"></div>
<?php $columns = [
	[
		'label' => 'ID',
		'value' => function($data) {
			return $data->invoice->getInvoiceNumber();
		}
	],
	'invoice.date:date',
	'description:text',
	[
		'label' => 'Total',
		'attribute' => 'amount',
		'format'=>['decimal',2],
		'contentOptions' => ['class' => 'text-right'],
		'hAlign'=>'right',
		'pageSummary'=>true,
		'pageSummaryFunc'=>GridView::F_SUM
	],	
]; ?>
<div class="grid-row-open">
    <div class="box">
        <?php
        echo GridView::widget([
            'dataProvider' => $royaltyFreeDataProvider,
            'summary' => '',
            'tableOptions' => ['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'rowOptions' => function ($model, $key, $index, $grid) {
                $url = Url::to(['invoice/view', 'id' => $model->invoice->id]);

                return ['data-url' => $url];
            },
            'pjax' => true,
            'showPageSummary' => true,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => [
                    'id' => 'tax-grid',
                ],
            ],
            'columns' => $columns,
]); ?>
    </div>
</div>
<script>
$(document).ready(function () {
	$("#reportsearch-summarizeresults").on("change", function() {
		var summariesOnly = $(this).is(":checked");
		var dateRage = $('#reportsearch-daterange').val();
		var params = $.param({ 'ReportSearch[summarizeResults]': (summariesOnly | 0),
			'ReportSearch[dateRange]': dateRage});
		var url = '<?php echo Url::to(['report/tax-collected']); ?>?' + params;
		$.pjax.reload({url:url,container:"#tax-grid",replace:false,  timeout: 6000});  
    });
});
</script>