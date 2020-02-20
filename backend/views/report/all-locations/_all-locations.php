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
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'showPageSummary' => true,
        'columns' => [
            [
                'label' => 'Name',
                'value' => 'locationName'
            ],
            [
            'label' => 'Active Enrolments',
            'value' =>  'activeEnrolmentsCount',
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
            'value' => 'revenue',
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Royalty',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => 'locationDebtValueRoyalty',
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Advertisement',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => 'locationDebtValueAdvertisement',
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'HST',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => 'taxAmount',
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Total',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'format' => 'currency',
            'value' => 'subTotal',
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