<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\GridView;
use common\models\LocationDebt;
use yii\helpers\Html;
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
    <?php
    echo GridView::widget([
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
        ],
            [
            'label' => 'Revenue',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($searchModel) {
                return !empty($data->getRevenue($searchModel->fromDate, $searchModel->toDate)) ? round($data->getRevenue($searchModel->fromDate, $searchModel->toDate), 2) : 0;
            },
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
        ],
    ],
]);

    ?>
<?php Pjax::end(); ?>