<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

?>
<?php if (!$searchModel->summarizeResults) : ?>
    <?php
    $columns = [
            [
            'value' => function ($data) {
                return (new \DateTime($data->invoice->date))->format('l, F jS, Y');
            },
            'group' => true,
            'groupedRow' => true,
            'groupFooter' => function ($model, $key, $index, $widget) {
                return [
                    'mergeColumns' => [[1, 2]],
                    'content' => [
                        3 => GridView::F_SUM,
                        4 => GridView::F_SUM,
                        5 => GridView::F_SUM,
                    ],
                    'contentFormats' => [
                        3 => ['format' => 'number', 'decimals' => 2],
                        4 => ['format' => 'number', 'decimals' => 2],
                        5 => ['format' => 'number', 'decimals' => 2],
                    ],
                    'contentOptions' => [
                        3 => ['style' => 'text-align:right'],
                        4 => ['style' => 'text-align:right'],
                        5 => ['style' => 'text-align:right'],
                    ],
                    'options' => ['style' => 'font-weight:bold;']
                ];
            }
        ],
            [
            'label' => 'Source ID',
            'value' => function ($data) {
                return $data->invoice->getInvoiceNumber();
            },
        ],
            [
            'label' => 'Customer',
            'value' => function ($data) {
                return !empty($data->invoice->user->publicIdentity) ? $data->invoice->user->publicIdentity : null;
            },
        ],
            [
            'label' => 'Subtotal',
            'format' => ['decimal', 2],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Tax',
            'format' => ['decimal', 2],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Total',
            'value' => function ($data) {
                return $data->amount + $data->tax_rate;
            },
            'format' => ['decimal', 2],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
    ];

    ?>   
<?php else : ?>
    <?php
    $columns = [
        'invoice.date:date',
            [
            'label' => 'Subtotal',
            'value' => function ($data) {
                return $data->getTaxLineItemAmount($data->invoice->date);
            },
            'format' => ['decimal', 2],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Tax',
            'value' => function ($data) {
                return $data->getTaxLineItemTotal($data->invoice->date);
            },
            'format' => ['decimal', 2],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Total',
            'value' => function ($data) {
                return $data->getTotal($data->invoice->date);
            },
            'format' => ['decimal', 2],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
    ];

    ?>
    <?php endif; ?>
<div class="box">
    <?php
    echo GridView::widget([
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
    ]);

    ?>
</div>