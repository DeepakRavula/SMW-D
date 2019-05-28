<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\LocationDebt;
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
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['customer/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'toolbar' =>  [
            '{export}',
            '{toggleData}'
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT
        ],
        'showPageSummary' => true,
        'columns' => [
            [
                'label' => 'Customer Name',
                'value' => function ($data) {
                    return  $data->userProfile->fullName;
                },
            ],
            [
                'label' => 'OutStanding Invoices',
                'value' => function ($data) {
                    return  $data->getInvoiceOwingAmountTotal($data->id) ? Yii::$app->formatter->asDecimal(round($data->getInvoiceOwingAmountTotal($data->id), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right dollar'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Pre-Paid Lessons',
                'value' => function ($data) {
                    return  $data->getPrePaidLessons($data->id) ? Yii::$app->formatter->asDecimal(round($data->getPrePaidLessons($data->id), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right dollar',],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Unused Credits',
                'value' => function ($data) {
                    return  $data->getTotalCredits($data->id) ? Yii::$app->formatter->asDecimal(round($data->getTotalCredits($data->id), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right dollar',],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Balance',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asDecimal(round($data->getInvoiceOwingAmountTotal($data->id), 2) - (round($data->getPrePaidLessons($data->id), 2) + round($data->getTotalCredits($data->id), 2)), 2);
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right dollar'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
        ]
]);

    ?>
<?php Pjax::end(); ?>