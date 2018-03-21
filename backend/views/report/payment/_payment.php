<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\InvoicePayment;
use common\models\Invoice;
use common\models\PaymentMethod;
use common\models\Payment;
use backend\assets\CustomGridAsset;

CustomGridAsset::register($this);
Yii::$app->assetManager->bundles['kartik\grid\GridGroupAsset'] = false;
 /*
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
	<?php if ($searchModel->groupByMethod) : ?>
		<?php
        $columns = [
                [
                'value' => function ($data) {
                    if (!empty($data->date)) {
                        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                        return $lessonDate->format('l, F jS, Y');
                    }

                    return null;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
                'group' => true,
                'groupedRow' => true,
                'groupFooter' => function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns' => [[1]],
                        'content' => [
                            2 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            2 => ['format' => 'number', 'decimals' => 2],
                        ],
                        'contentOptions' => [
                            2 => ['style' => 'text-align:right'],
                        ],
                        'options' => ['style' => 'font-weight:bold;']
                    ];
                }
            ],
                [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
            ],
                [
                'label' => 'Amount',
                'value' => function ($data) use ($searchModel) {
                    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
                    $amount = 0;
                    $payments = Payment::find()
                        ->location($locationId)
                        ->andWhere([
                            'payment_method_id' => $data->payment_method_id,
                            'DATE(payment.date)' => (new \DateTime($data->date))->format('Y-m-d')
                        ])
                        ->all();
                    foreach ($payments as $payment) {
                        $amount += $payment->amount;
                    }

                    return Yii::$app->formatter->asDecimal($amount,2);
                },
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
                [
                'value' => function ($data) {
                    if (!empty($data->date)) {
                        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                        return $lessonDate->format('Y-m-d');
                    }

                    return null;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
                'group' => true,
                'groupedRow' => true,
                'groupFooter' => function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns' => [[2, 3]],
                        'content' => [
                            5 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            5 => ['format' => 'number', 'decimals' => 2],
                        ],
                        'contentOptions' => [
                            5 => ['style' => 'text-align:right'],
                        ],
                        'options' => ['style' => 'font-weight:bold;font-size:14px;']
                    ];
                }
            ],
                [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left'],
                'group' => true,
                'groupedRow' => true,
                'subGroupOf' => 0,
                'groupFooter' => function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns' => [[2, 4]],
                        'content' => [
                            5 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            5 => ['format' => 'number', 'decimals' => 2],
                        ],
                        'contentOptions' => [
                            5 => ['style' => 'text-align:right'],
                        ],
                        'options' => ['class' => 'success', 'style' => 'font-weight:bold;font-size:14px']
                    ];
                },
            ],
                [
                'label' => 'ID',
                'value' => function ($data) {
                    return $data->invoicePayment->invoice->getInvoiceNumber();
                },
                'contentOptions' => ['style' => 'font-size:14px'],
            ],
                [
                'label' => 'Customer',
                'value' => function ($data) {
                    return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                },
                'contentOptions' => ['style' => 'font-size:14px'],
            ],
                [
                'label' => 'Reference',
                'contentOptions' => ['style' => 'font-size:14px'],
                'value' => function ($data) {
                    if ((int) $data->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_APPLIED || (int) $data->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_USED) {
                        $invoiceNumber = str_pad($data->reference, 5, 0, STR_PAD_LEFT);
                        $invoicePayment = InvoicePayment::findOne(['payment_id' => $data->id]);
                        if ((int) $invoicePayment->invoice->type === Invoice::TYPE_INVOICE) {
                            return 'I - ' . $invoiceNumber;
                        } else {
                            return 'P - ' . $invoiceNumber;
                        }
                    } else {
                        return $data->reference;
                    }
                },
            ],
                [
                'label' => 'Amount',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDecimal($data->amount,2);
                },
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
        ];
        ?>
	<?php endif; ?>

            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'emptyText' => false,
                'options' => ['class' => ''],
                'showPageSummary' => true,
                'headerRowOptions' => ['class' => 'bg-light-gray'],
                'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
                'pjax' => true,
                'pjaxSettings' => [
                    'neverTimeout' => true,
                    'options' => [
                        'id' => 'payment-listing',
                    ],
                ],
                'columns' => $columns,
            ]);
            ?>
