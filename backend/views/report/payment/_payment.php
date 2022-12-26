<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\InvoicePayment;
use common\models\Invoice;
use common\models\Location;
use common\models\PaymentMethod;
use common\models\Payment;
use backend\assets\CustomGridAsset;
use common\components\gridView\KartikGridView;

CustomGridAsset::register($this);
Yii::$app->assetManager->bundles['kartik\grid\GridGroupAsset'] = false;
 /*
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
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
</style>
<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
<?php $locationId = Location::findOne(['slug' => \Yii::$app->location])->id; ?>
	<?php if ($searchModel->groupByMethod) : ?>
		<?php $columns = [
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
                'groupFooter' => function ($model, $key, $index, $widget) use ($locationId) {
                    $paymentsAmount = Payment::find()
                        ->exceptAutoPayments()
                        ->exceptGiftCard()
                        ->location($locationId)
                        ->notDeleted()
                        ->andWhere(['between', 'DATE(payment.date)', (new \DateTime($model->date))->format('Y-m-d'), 
                            (new \DateTime($model->date))->format('Y-m-d')])
                        ->sum('payment.amount');
                    return [
                        'mergeColumns' => [[1]],
                        'content' => [
                            2 => Yii::$app->formatter->asCurrency(round($paymentsAmount, 2)),
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
                    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
                    $amount = 0;
                    $payments = Payment::find()
                        ->notDeleted()
                        ->location($locationId)
                        ->andWhere([
                            'payment_method_id' => $data->payment_method_id,
                            'DATE(payment.date)' => (new \DateTime($data->date))->format('Y-m-d')
                        ])
                        ->notDeleted()
                        ->all();
                    foreach ($payments as $payment) {
                        $amount += $payment->amount;
                    }

                    return Yii::$app->formatter->asCurrency(round($amount, 2));
                },
                'contentOptions' => ['class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => function ($summary, $data, $widget) use ($paymentsAmount) { 
                    return Yii::$app->formatter->asCurrency(round($paymentsAmount, 2)); 
                }
            ],
        ];
        ?>
	<?php else : ?>
        <?php $columns = [
            [
                'value' => function ($data) {
                    if (!empty($data->date)) {
                        $lessonDate = Yii::$app->formatter->asDate($data->date);
                        return $lessonDate;
                    }

                    return null;
                },
                'group' => true,
                'contentOptions' => ['style' => 'font-weight:bold; font-size:14px; text-align:left', 'class' => 'main-group'],
                'groupedRow' => true,
                'groupFooter' => function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns' => [[2, 3]],
                        'content' => [
                            5 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            5 => ['format' => 'number', 'decimals' => 2, 'thousandSep' => ','],
                        ],
                        'contentOptions' => [
                            5 => ['style' => 'font-size:15px; text-align:right', 'class' => 'dollar'],
                        ],
                        'options' => ['style' => 'font-weight:bold', 'class' => 'info table-info']
                    ];
                }

            ],
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
                'group' => true,
                'subGroupOf' => 0,
                'contentOptions' => ['style' => 'width:200px; font-weight:bold; font-size:14px; text-align:left', 'class' => 'main-group'],

                'groupFooter' => function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns' => [[2, 4]],
                        'content' => [
                            5 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            5 => ['format' => 'number', 'decimals' => 2, 'thousandSep' => ','],
                        ],
                        'contentOptions' => [
                            5 => ['style' => 'text-align:right', 'class' => 'dollar'],
                        ],
                        'options' => ['class' => 'success table-success', 'style' => 'font-weight:bold;']
                    ];
                },
            ],
            [
                'label' => 'Payment ID',
                'value' => function ($data) {
                    return $data->getPaymentNumber();
                },
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
                'format' => ['decimal', 2],
                'value' => function ($data) {
                    return $data->amount;
                },
                'contentOptions' => ['style' => 'text-align:right;', 'class' => 'dollar'],
                'pageSummaryOptions' => ['style' => 'text-align:right', 'class' => 'dollar'],
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
        ];
        ?>
    <?php endif; ?>
    
    <?= KartikGridView::widget([
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
        'toolbar' => [
            ['content' => $this->render('_button', [
                'model' => $searchModel
                ])],
            ['content' => Html::a('<i class="fa fa-print btn-default btn-lg"></i>', '#', ['id' => 'print'])],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Payments'
        ],
    ]); ?>