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
                            8 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            8 => ['format' => 'number', 'decimals' => 2],
                        ],
                        'contentOptions' => [
                            8 => ['style' => 'text-align:right'],
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
                'label' => 'PF',
                'hAlign' => 'center',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px'],
                'value' => function ($data) {
                    return $data->enrolment ? $data->enrolment->getPaymentFrequency() : null;
                },
            ],
            [
                'label' => 'PF Discount(%)',
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px; width:107px;'],
                'value' => function ($data) {
                    if ($data->enrolmentPaymentFrequencyDiscount) {
                        return $data->enrolmentPaymentFrequencyDiscount->value != 0.00 ?
                            $data->enrolmentPaymentFrequencyDiscount->value : null;
                    }
                }
            ],
            [
                'label' => 'Enrolment Discount($)',
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px; width:85px;'],
                'value' => function ($data) {
                    if ($data->multiEnrolmentDiscount) {
                        return $data->multiEnrolmentDiscount->value != 0.00 ?
                            $data->multiEnrolmentDiscount->value : null;
                    }
                }
            ],
            [
                'format' => ['decimal', 2],
                'label' => 'Other Discount($)',
                'hAlign' => 'right',
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px; width:122px;'],
                'value' => function ($data) {
                    if ($data->lineItemDiscount || $data->customerDiscount) {
                        return $data->getOtherDiscountValue() != 0.00 ?
                            $data->getOtherDiscountValue() : null;
                    }
                }
            ],
            [
                'format' => ['decimal', 2],
                'label' => 'Net Discount($)',
                'value' => function ($data) {
                    return $data->discount;
                },
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px; width:108px;'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'format' => ['decimal', 2],
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
    ]); ?>
</div>