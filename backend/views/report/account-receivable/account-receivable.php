<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
?>
<style>
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
        'rowOptions' =>  ['class' => 'account-receivable-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'toolbar' =>  [
            'content' =>
                    Html::a('<i class="fa fa-print"></i>', '#', 
                    ['id' => 'print', 'class' => 'btn btn-default']),
            '{export}',
            '{toggleData}'
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => '<h3 class = "f-s-20"> Accounts Receivable </h3>',
        ],
        'showPageSummary' => true,
        'columns' => [
            [
                'label' => 'Customer Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'value' => function ($data) {
                    return  $data->userProfile ? $data->userProfile->fullName : null;
                },
            ],
            [
                'label' => '0-30',
                'format' => 'currency',
                'value' => function ($data) {
                    return  $data->getRecentInvoicesBalanceTotal(30) ? Yii::$app->formatter->asDecimal(round($data->getRecentInvoicesBalanceTotal(30), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => '31-60',
                'format' => 'currency',
                'value' => function ($data) {
                    return  $data->getRecentInvoicesBalanceTotal(60) ? Yii::$app->formatter->asDecimal(round($data->getRecentInvoicesBalanceTotal(60), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => '61-90',
                'format' => 'currency',
                'value' => function ($data) {
                    return  $data->getRecentInvoicesBalanceTotal(90) ? Yii::$app->formatter->asDecimal(round($data->getRecentInvoicesBalanceTotal(90), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Total',
                'format' => 'currency',
                'value' => function ($data) {
                    return  $data->getInvoiceOwingAmountTotal($data->id) ? Yii::$app->formatter->asDecimal(round($data->getInvoiceOwingAmountTotal($data->id), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Pre-Paid Lessons',
                'format' => 'currency',
                'value' => function ($data) {
                    return  $data->getPrePaidLessons($data->id) ? Yii::$app->formatter->asDecimal(round($data->getPrePaidLessons($data->id), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right',],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Unused Credits',
                'format' => 'currency',
                'value' => function ($data) {
                    return  $data->getTotalCredits($data->id) ? Yii::$app->formatter->asDecimal(round($data->getTotalCredits($data->id), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right',],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Balance',
                'format' => 'currency',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asDecimal(round($data->getInvoiceOwingAmountTotal($data->id), 2) - (round($data->getPrePaidLessons($data->id), 2) + round($data->getTotalCredits($data->id), 2)), 2);
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
        ],
        'beforeHeader'=>[
            [
                'columns'=>[
                    ['content'=>'', 'options'=>['colspan'=>0, 'class'=>'text-center warning']], 
                    ['content'=>'OutStanding Invoices', 'options'=>['colspan'=>5, 'class'=>'text-center warning']], 
                    ['content'=>'', 'options'=>['colspan'=>3, 'class'=>'text-center warning']], 
                ],
                'options'=>['class'=>'skip-export'] // remove this row from export
            ]
        ],
]);

    ?>
<?php Pjax::end(); ?>
<script>
$(document).off('click', '.account-receivable-report-detail-view').on('click', '.account-receivable-report-detail-view', function() { 
    var userId = $(this).attr('data-key');
    var params = $.param({ 'id' : userId});
    var url = '<?= Url::to(['account-receivable-report/view']) ?>?' + params;
    window.open(url, '_blank');
        return false;   
     });
</script>