<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
$total = 0;
$unusedCredits = 0;
$prePaidLessonsSum = 0;
$balance = 0;
$sum90Plus = 0;
$sum90Days = 0;
$sum60days = 0;
$sum30days = 0;
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
            'heading' => 'Accounts Receivable',
        ],
        'showFooter' =>true,
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
                'value' => function ($data, $key, $index, $widget) use(&$sum30Days) {
                    $sum30Days += $data->getRecentInvoicesBalanceTotal(30) ? round($data->getRecentInvoicesBalanceTotal(30), 2) : 0;
                    $widget->footer =  Yii::$app->formatter->asCurrency($sum30Days);
                    return  $data->getRecentInvoicesBalanceTotal(30) ? Yii::$app->formatter->asCurrency(round($data->getRecentInvoicesBalanceTotal(30), 2)) : '$0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => '31-60',
                'value' => function ($data, $key, $index, $widget) use(&$sum60Days) {
                    $sum60Days += $data->getRecentInvoicesBalanceTotal(60) ? round($data->getRecentInvoicesBalanceTotal(60), 2) : 0;
                    $widget->footer =  Yii::$app->formatter->asCurrency($sum60Days);
                    return  $data->getRecentInvoicesBalanceTotal(60) ? Yii::$app->formatter->asCurrency(round($data->getRecentInvoicesBalanceTotal(60), 2)) : '$0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => '61-90', 
                'value' => function ($data, $key, $index, $widget) use(&$sum90Days) {
                    $sum90Days += $data->getRecentInvoicesBalanceTotal(90) ? round($data->getRecentInvoicesBalanceTotal(90), 2) : 0;
                    $widget->footer =  Yii::$app->formatter->asCurrency($sum90Days);
                    return  $data->getRecentInvoicesBalanceTotal(90) ? Yii::$app->formatter->asCurrency(round($data->getRecentInvoicesBalanceTotal(90), 2)) : '$0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => '90+',
                'value' => function ($data, $key, $index, $widget) use(&$sum90Plus) {
                    $sum90Plus += $data->getRecentInvoicesBalanceTotal(91) ? round($data->getRecentInvoicesBalanceTotal(91), 2) : 0;
                    $widget->footer =  Yii::$app->formatter->asCurrency($sum90Plus);
                    return  $data->getRecentInvoicesBalanceTotal(91) ? Yii::$app->formatter->asCurrency(round($data->getRecentInvoicesBalanceTotal(91), 2)) : '$0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Total',
                'value' => function ($data, $key, $index, $widget) use(&$total) {
                    $total += $data->getInvoiceOwingAmountTotal($data->id) ? round($data->getInvoiceOwingAmountTotal($data->id), 2) : 0;
                    $widget->footer =  Yii::$app->formatter->asCurrency($total);
                    return  $data->getInvoiceOwingAmountTotal($data->id) ? Yii::$app->formatter->asCurrency(round($data->getInvoiceOwingAmountTotal($data->id), 2)) : '$0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Pre-Paid Lessons',
                'value' => function ($data, $key, $index, $widget) use(&$prePaidLessonsSum) {
                    $prePaidLessonsSum  += $data->getPrePaidLessons($data->id) ? round($data->getPrePaidLessons($data->id), 2) : 0;
                    $widget->footer =  Yii::$app->formatter->asCurrency($prePaidLessonsSum);
                    return  $data->getPrePaidLessons($data->id) ? Yii::$app->formatter->asCurrency(round($data->getPrePaidLessons($data->id), 2)) : '$0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right',],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Unused Credits',
                'value' => function ($data, $key, $index, $widget) use (&$unusedCredits) {
                    $unusedCredits = $unusedCredits + round($data->getTotalCredits($data->id), 2);
                    $widget->footer =  Yii::$app->formatter->asCurrency($unusedCredits);
                    return  $data->getTotalCredits($data->id) ? Yii::$app->formatter->asCurrency(round($data->getTotalCredits($data->id), 2)) : '$0.00';
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right',],
                'hAlign' => 'right',
            ],
            [
                'label' => 'Balance',
                'value' => function ($data, $key, $index, $widget) use (&$balance) {
                    $balance += (round($data->getInvoiceOwingAmountTotal($data->id), 2) - (round($data->getPrePaidLessons($data->id), 2) + round($data->getTotalCredits($data->id), 2)));
                    $widget->footer = Yii::$app->formatter->asCurrency($balance);
                    return  Yii::$app->formatter->asCurrency(round($data->getInvoiceOwingAmountTotal($data->id), 2) - (round($data->getPrePaidLessons($data->id), 2) + round($data->getTotalCredits($data->id), 2)));
                },
                'headerOptions' => ['class' => 'text-right warning', 'style' => 'background-color: lightgray'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right'],
                'hAlign' => 'right',
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