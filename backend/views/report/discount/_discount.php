<?php

use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use yii\helpers\Html;

?>
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
                            9 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            9 => ['format' => 'number', 'decimals' => 2],
                        ],
                        'contentOptions' => [
                            9 => ['style' => 'text-align:right'],
                        ],
                        'options' => ['style' => 'font-weight:bold;font-size:14px;']
                    ];
                }
            ],
            [
                'label' => 'Code',
                'value' => function ($data) {
                    return $data->code;
                },
                'contentOptions' => ['style' => 'font-size:14px;width:150px'],
            ],
            [
                'label' => 'Description',
                'value' => function ($data) {
                    return substr($data->description, 0, 35).' ...';
                },
                'contentOptions' => ['style' => 'font-size:14px;width:250px'],
            ],
            
            [
                'label' => 'PF',
                'hAlign' => 'left',
                'contentOptions' => ['class' => 'text-left', 'style' => 'font-size:14px;width:150px'],
                'value' => function ($data) {
                    return $data->enrolment ? $data->enrolment->getPaymentFrequency() : null;
                },
            ],
            [
                'label' => 'Qty',
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px;width:50px'],
                'value' => function ($data) {
                    return $data->unit;
                },
            ],
            [
                'label' => 'PF(%)',
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px; width:60px;'],
                'value' => function ($data) {
                    if ($data->enrolmentPaymentFrequencyDiscount) {
                        return $data->enrolmentPaymentFrequencyDiscount->value != 0.00 ?
                            floatval($data->enrolmentPaymentFrequencyDiscount->value) : null;
                    }
                }
            ],
            [
                'label' => 'Enrol($)',
		'format' => ['decimal', 2],
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px; width:80px;'],
                'value' => function ($data) {
                    if ($data->multiEnrolmentDiscount) {
                        return $data->multiEnrolmentDiscount->value != 0.00 ?
                            floatval($data->multiEnrolmentDiscount->value) : null;
                    }
                }
            ],
            [
                'label' => 'Customer(%)',
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px; width:100px;'],
                'value' => function ($data) {
                    if ($data->customerDiscount) {
                        return $data->customerDiscount->value != 0.00 ?
                            floatval($data->customerDiscount->value) : null;
                    }
                }
            ],
            [
                'label' => 'Item($)',
		'format' => ['decimal', 2],
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px; width:60px;'],
                'value' => function ($data) {
                    if ($data->lineItemDiscount) {
                        return floatval($data->getLineItemDiscountValue());
                    }
                }
            ],
            [
                'label' => 'Net($)',
                'format' => ['decimal', 2],
                'value' => function ($data) {
                    return floatval($data->discount);
                },
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px; width:60px;'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'format' => ['decimal', 2],
                'label' => 'Price',
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px;width:60px'],
                'value' => function ($data) {
                    return $data->itemTotal;
                },
            ],
        ];
    ?>

    <?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => ''],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '-'],
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
        'toolbar' => [
            ['content' => Html::a('<i class="fa fa-print btn-default btn-lg"></i>', '#', ['id' => 'print'])],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Discount Report'
        ],
    ]); ?>
