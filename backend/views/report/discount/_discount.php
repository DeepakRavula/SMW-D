<?php

use kartik\grid\GridView;

?>
<div class="payments-index">
    <?php
        $columns = [
            [
                'label' => 'Customer',
                'value' => function ($data) {
                    return !empty($data->invoice->user->publicIdentity) ? $data->invoice->user->publicIdentityWithEnrolment : null;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
                'group' => true,
                'groupedRow' => true,
                'groupFooter' => function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns' => [[2, 3]],
                        'content' => [
                            4 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            4 => ['format' => 'number', 'decimals' => 2],
                        ],
                        'contentOptions' => [
                            4 => ['style' => 'text-align:right'],
                        ],
                        'options' => ['style' => 'font-weight:bold;font-size:14px;']
                    ];
                }
            ],
            [
                'label' => 'Code',
                'value' => function($data) {
                    return $data->code;
                },
                'contentOptions' => ['style' => 'font-size:14px'],
            ],
            [
                'label' => 'Description',
                'value' => function ($data) {
                    return $data->description;
                },
                'contentOptions' => ['style' => 'font-size:14px'],
            ],
            [
                'label' => 'Qty',
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px'],
                'value' => function ($data) {
                    return $data->unit;
                },
            ],
            [
                'label' => 'Discount',
                'value' => function ($data) {
                    return $data->discount;
                },
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Price',
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px'],
                'value' => function ($data) {
                    return $data->netPrice;
                },
            ],
        ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => ''],
        'showPageSummary' => true,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'discount-report',
            ],
        ],
        'columns' => $columns,
    ]); ?>
</div>