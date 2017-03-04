<?php

use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?php $columns = [
	[
		'label' => 'Code',
        'value' => function ($data) {
            return $data->itemType->itemCode;
        },
	],
	'description',
	[
		'label' => 'Qty',
		'attribute' => 'unit',
		'format'=>['decimal',2],
		'contentOptions' => ['class' => 'text-right'],
		'hAlign'=>'right',
		'pageSummary'=>true,
		'pageSummaryFunc'=>GridView::F_SUM
	],
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
<?php echo GridView::widget([
	'dataProvider' => $royaltyDataProvider,
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'showPageSummary' => true,
	'columns' => $columns,
]); ?>

