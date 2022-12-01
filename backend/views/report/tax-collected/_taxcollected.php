<?php

use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use yii\helpers\Html;
use common\models\Location;
use common\models\Invoice;

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
.table > tbody >tr.warning >td {
    font-size:17px;
}
.kv-page-summary {
    border-top:none;
    font-weight: bold;
}
.table > tbody + tbody {
     border-top: none;
}
td.kv-group-even {
    background-color: white!important;
}
td.kv-group-odd {
    background-color: white!important;
}
</style>
    <?php
    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id; ?>
    <?php if (!$searchModel->summarizeResults) : ?>
    <?php $columns = [
        [
            'value' => function ($data) {
                return (new \DateTime($data->date))->format('l, F jS, Y');
            },
            'group' => true,
            'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
            'groupedRow' => true,
            'groupFooter' => function ($model, $key, $index, $widget) {    
                return [
                    'mergeColumns' => [[1, 2]],
                    'content' => [
                        3 => GridView::F_SUM,
                        4 => GridView::F_SUM,
                        5 => GridView::F_SUM
                    ],
                    'contentFormats' => [
                        3 => ['format' => 'number', 'decimals' => 2, 'thousandSep'=>','],
                        4 => ['format' => 'number', 'decimals' => 2, 'thousandSep'=>','],
                        5 => ['format' => 'number', 'decimals' => 2, 'thousandSep'=>','],
                    ],
                    'contentOptions' => [
                        3 => ['style' => 'text-align:right', 'class' => 'dollar'],
                        4 => ['style' => 'text-align:right', 'class' => 'dollar'],
                        5 => ['style' => 'text-align:right', 'class' => 'dollar'],
                    ],
                    'options' => ['style' => 'font-weight:bold;']
                ];
            }
        ],
        [
            'label' => 'Source ID',
            'value' => function ($data) {
                return $data->getInvoiceNumber();
            }
        ],
        [
            'label' => 'Customer',
            'value' => function ($data) {
                return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
            }
        ],
        [
            'label' => 'Subtotal',
            'format' => ['decimal', 2],
            'value' => function ($data) {
                return round($data->subTotal, 2);
            },
            'contentOptions' => ['class' => 'text-right dollar'],
            'hAlign' => 'right',
            'pageSummaryOptions' => ['class' => 'dollar'],
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
        [
            'label' => 'Tax',
            'format' => ['decimal', 2],
	        'value' => function ($data) {
                return $data->tax;
            },
            'contentOptions' => ['class' => 'text-right dollar'],
            'hAlign' => 'right',
            'pageSummaryOptions' => ['class' => 'dollar'],
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
        [
            'label' => 'Total',
            'format' => ['decimal', 2],
            'value' => function ($data) {
                return round($data->total, 2);
            },
            'contentOptions' => ['class' => 'text-right dollar'],
            'hAlign' => 'right',
            'pageSummaryOptions' => ['class' => 'dollar'],
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ]
    ];

    ?>  
    <?php else : ?>
    <?php
    $columns = [
        [
            'label' => 'Date',
            'value' => function ($data) {
                return (new \DateTime($data->date))->format('l, F jS, Y');
            },
            'group' => true,
            'contentOptions' => ['style' => 'font-size:14px;text-align:left;','class'=>'main-group'],
        ],
            [
            'label' => 'Subtotal',
            'value' => function ($data) use ($locationId) {
                $invoiceTaxes = Invoice::find()
                    ->notDeleted()
                    ->location($locationId)
                    ->invoice()
                    ->andWhere(['between', 'DATE(invoice.date)', (new \DateTime($data->date))->format('Y-m-d'), 
                        (new \DateTime($data->date))->format('Y-m-d')]);
                return Yii::$app->formatter->asCurrency(round($invoiceTaxes->sum('subTotal'), 2));
            },
            'group' => true,
            'subGroupOf' => 0,
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => function ($summary, $data, $widget) use ($subtotalSum) { 
                return Yii::$app->formatter->asCurrency(round($subtotalSum, 2)); 
            }
        ],
            [
            'label' => 'Tax',
            'value' => function ($data) use ($locationId) {
                $invoiceTaxes = Invoice::find()
                    ->notDeleted()
                    ->location($locationId)
                    ->invoice()
                    ->andWhere(['between', 'DATE(invoice.date)', (new \DateTime($data->date))->format('Y-m-d'), 
                        (new \DateTime($data->date))->format('Y-m-d')]);
                return Yii::$app->formatter->asCurrency(round($invoiceTaxes->sum('tax'), 2));
            },
            'group' => true,
            'subGroupOf' => 0,
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => function ($summary, $data, $widget) use ($taxSum) { 
                return Yii::$app->formatter->asCurrency(round($taxSum, 2)); 
            }
        ],
            [
            'label' => 'Total',
            'value' => function ($data) use ($locationId) {
                $invoiceTaxes = Invoice::find()
                    ->notDeleted()
                    ->location($locationId)
                    ->invoice()
                    ->andWhere(['between', 'DATE(invoice.date)', (new \DateTime($data->date))->format('Y-m-d'), 
                        (new \DateTime($data->date))->format('Y-m-d')]);
                return Yii::$app->formatter->asCurrency(round($invoiceTaxes->sum('total'), 2));
            },
            'group' => true,
            'subGroupOf' => 0,
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => function ($summary, $data, $widget) use ($totalSum) { 
                return Yii::$app->formatter->asCurrency(round($totalSum, 2)); 
            }
        ],
    ];

    ?>
<?php endif; ?>

<div class="box">
    <?= KartikGridView::widget([
        'dataProvider' => $taxDataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'pjax' => true,
        'showPageSummary' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'tax-grid',
            ],
        ],
        'columns' => $columns,
        'toolbar' => [
            ['content' => $this->render('_button', [
                'model' => $searchModel
                ])],
            ['content' => Html::a('<i class="fa fa-print btn-default btn-lg"></i>', '#', ['id' => 'print'])],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Tax Collected'
        ],
    ]); ?>
</div>