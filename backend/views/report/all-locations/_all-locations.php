<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\LocationDebt;
use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
?>
<style>
  .table > tbody > tr.success > td ,.table > tbody > tr.kv-grid-group-row > td{
	background-color: white !important;
}
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: white !important;
}
.table > thead:first-child > tr:first-child > th{
    color : black;
    background-color : lightgray;
}
</style>
<div class="clearfix"></div>
    <?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['student/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'showPageSummary' => true,
        'columns' => [
            [
                'label' => 'Name',
                'value' => function ($data) {
                    return  $data->name;
                },
            ],
            [
            'label' => 'Active Students',
            'value' => function ($data) use ($searchModel) {
                return !empty($data->getActiveStudentsCount($searchModel->fromDate, $searchModel->toDate)) ? $data->getActiveStudentsCount($searchModel->fromDate, $searchModel->toDate) : 0;
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Revenue',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($searchModel) {
                return !empty($data->getRevenue($searchModel->fromDate, $searchModel->toDate)) ? round($data->getRevenue($searchModel->fromDate, $searchModel->toDate), 2) : 0;
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Royalty',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($searchModel) {
                $royaltyValue = $data->getLocationDebt(LocationDebt::TYPE_ROYALTY, $searchModel->fromDate, $searchModel->toDate);
                return round($royaltyValue, 2);
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Advertisement',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($searchModel) {
                $advertisementValue = $data->getLocationDebt(LocationDebt::TYPE_ADVERTISEMENT, $searchModel->fromDate, $searchModel->toDate);
                return round($advertisementValue, 2);
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'HST',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($searchModel) {
                $taxAmount = $data->getTaxAmount($searchModel->fromDate, $searchModel->toDate);
                return round($taxAmount, 2);
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Total',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'format' => 'currency',
            'value' => function ($data) use ($searchModel) {
                $subTotal = $data->SubTotal($searchModel->fromDate, $searchModel->toDate);
                $taxAmount = $data->getTaxAmount($searchModel->fromDate, $searchModel->toDate);
                $total = $subTotal + $taxAmount;
                return round($total, 2);
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
   ],
    'panel' => [
        'heading' => 'All Locations',
    ],
        'toolbar' =>  [
             [
 			'content'=> $this->render('_search', ['model' => $searchModel])
 				
 		],
 		   [
 		       'content'=>
 		       Html::a('<i class="glyphicon glyphicon-print"></i>','#',
 					[
 						'id' => 'print',
 						'class'=>'btn btn-default',
 					]
 				),
 		     ],
            '{export}',
        ],  
        'export' => [
            'fontAwesome' => true,
        ], 
]);

    ?>
<?php Pjax::end(); ?>
<script>
        $(document).on("click", "#print", function () {
            var dateRange = $('#reportsearch-daterange').val();
            var params = $.param({'ReportSearch[dateRange]': dateRange});
            var url = '<?php echo Url::to(['print/all-locations']); ?>?' + params;
            window.open(url, '_blank');
        });
</script> 