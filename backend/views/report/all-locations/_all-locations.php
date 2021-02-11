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
<?php $total = 0;
$activeEnrolments = 0;
$revenue = 0;
$royalty = 0;
$locationDebtValueAdvertisement =0;
$taxAmount = 0;?>
</style>
<div class="clearfix"></div>
    <?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'showFooter' =>true,
        'columns' => [
            [
                'label' => 'Name',
                'value' => 'locationName'
            ],
            [
            'label' => 'Active Enrolments',
            'value' => function ($data, $key, $index, $widget) use(&$activeEnrolments) {
                $activeEnrolments += $data['activeEnrolmentsCount'];
                $widget->footer =  $activeEnrolments;
                return   $data['activeEnrolmentsCount'];
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
        ],
            [
            'label' => 'Revenue',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data, $key, $index, $widget) use(&$revenue) {
                $revenue += $data['revenue'];
                $widget->footer =  Yii::$app->formatter->asCurrency($revenue);
                return   Yii::$app->formatter->asCurrency($data['revenue']);
            },
            'hAlign' => 'right',
        ],
            [
            'label' => 'Royalty',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'value' => function ($data, $key, $index, $widget) use(&$royalty) {
                $royalty += $data['locationDebtValueRoyalty'];
                $widget->footer =  Yii::$app->formatter->asCurrency($royalty);
                return   Yii::$app->formatter->asCurrency($data['locationDebtValueRoyalty']);
            },
        ],
            [
            'label' => 'Advertisement',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => 'locationDebtValueAdvertisement',
            'hAlign' => 'right',
            'value' => function ($data, $key, $index, $widget) use(&$locationDebtValueAdvertisement) {
                $locationDebtValueAdvertisement += $data['locationDebtValueAdvertisement'];
                $widget->footer =  Yii::$app->formatter->asCurrency($locationDebtValueAdvertisement);
                return   Yii::$app->formatter->asCurrency($data['locationDebtValueAdvertisement']);
            },
        ],
            [
            'label' => 'HST',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => 'taxAmount',
            'hAlign' => 'right',
            'value' => function ($data, $key, $index, $widget) use(&$taxAmount) {
                $taxAmount += $data['taxAmount'];
                $widget->footer =  Yii::$app->formatter->asCurrency($taxAmount);
                return   Yii::$app->formatter->asCurrency($data['taxAmount']);
            },
        ],
            [
            'label' => 'Total',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data, $key, $index, $widget) use(&$total) {
                $total += $data['total'];
                $widget->footer =  Yii::$app->formatter->asCurrency($total);
                return   Yii::$app->formatter->asCurrency($data['total']);
            },
            'hAlign' => 'right',
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