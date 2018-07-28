<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

?>
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
                        5 => GridView::F_SUM,
                        6 => GridView::F_SUM,
                        7 => GridView::F_SUM,
                    ],
                    'contentFormats' => [
                        5 => ['format' => 'number', 'decimals' => 2],
                        6 => ['format' => 'number', 'decimals' => 2],
                        7 => ['format' => 'number', 'decimals' => 2],
                    ],
                    'contentOptions' => [
                        5 => ['style' => 'text-align:right'],
                        6 => ['style' => 'text-align:right'],
                        7 => ['style' => 'text-align:right'],
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
            'label' => 'Description',
            'value' => function ($data) {
                return $data->description ?? null;
            },
        ],
        [
            'label' => 'Qty',
            'value' => function ($data) {
                return $data->unit ?? null;
            },
        ],
            [
            'label' => 'Extended Price',
	    'value' => function ($data) {
                return round(($data->amount * $data->unit), 2);
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
                return $data->tax_rate;
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
                $extendedPrice = $data->amount * $data->unit;
                return round(($extendedPrice + $data->tax_rate), 2);
            },
            'format' => ['decimal', 2],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
    ];

    ?>   
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