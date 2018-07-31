<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Location;
use common\models\Invoice;

?>
    <?php
    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
    $columns = [
        [
            'value' => function ($data) {
                return (new \DateTime($data->date))->format('l, F jS, Y');
            },
            'group' => true,
            'groupedRow' => true,
            'groupFooter' => function ($model, $key, $index, $widget) use ($locationId) {
                $invoiceTaxes = Invoice::find()
                    ->notDeleted()
                    ->location($locationId)
                    ->invoice()
                    ->andWhere(['between', 'DATE(invoice.date)', (new \DateTime($model->date))->format('Y-m-d'), 
                        (new \DateTime($model->date))->format('Y-m-d')])
                    ->andWhere(['>', 'tax', 0]);
                    
                return [
                    'mergeColumns' => [[1, 2]],
                    'content' => [
                        3 => Yii::$app->formatter->asCurrency(round($invoiceTaxes->sum('subTotal'), 2)),
                        4 => Yii::$app->formatter->asCurrency(round($invoiceTaxes->sum('tax'), 2)),
                        5 => Yii::$app->formatter->asCurrency(round($invoiceTaxes->sum('total'), 2)),
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
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency(round($data->subTotal, 2));
            },
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => function ($summary, $data, $widget) use ($subtotalSum) { 
                return Yii::$app->formatter->asCurrency(round($subtotalSum, 2)); 
            }
        ],
        [
            'label' => 'Tax',
	        'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data->tax);
            },
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => function ($summary, $data, $widget) use ($taxSum) { 
                return Yii::$app->formatter->asCurrency(round($taxSum, 2)); 
            }
        ],
        [
            'label' => 'Total',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency(round($data->total, 2));
            },
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => function ($summary, $data, $widget) use ($totalSum) { 
                return Yii::$app->formatter->asCurrency(round($totalSum, 2)); 
            }
        ]
    ];

    ?>  

<div class="box">
    <?= GridView::widget([
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
    ]); ?>
</div>