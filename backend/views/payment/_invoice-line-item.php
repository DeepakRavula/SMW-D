<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
?>

<?php 
    $form = ActiveForm::begin([
        'id' => 'modal-form-invoice',
        'enableClientValidation' => false
    ]);
?>
<?php
    $columns = [
	[
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'Date',
            'value' => function ($data) {
                return  !empty($data->date) ? Yii::$app->formatter->asDate($data->date): null;
            }
        ],
	[
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'Number',
            'value' => function ($data) {
                return $data->invoiceNumber;
            }
        ],
        [
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'label' => 'Amount',
            'value' => function ($data) {
                return !empty($data->total) ? Yii::$app->formatter->asCurrency(round($data->total, 2)) : null;
            }
        ],
        [   
            'label' => 'Payment',
            'value' => function ($data) use($model) {
                return Yii::$app->formatter->asCurrency(round($data->getPaidAmount($model->id), 2));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ],
        [
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right invoice-value'],
            'label' => 'Balance',
            'value' => function ($data) use ($model, $canEdit) {
                $balance = $data->balance;
                if ($canEdit) {
                    $balance += $data->getPaidAmount($model->id);
                }
                return Yii::$app->formatter->asCurrency(round($balance, 2));
            }
        ]
    ];
        
    if ($canEdit) {
        array_push($columns, [
            'headerOptions' => ['class' => 'text-right', 'style' => 'width:180px'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:180px'],
            'label' => 'Payment',
            'value' => function ($data) use ($form, $model) {
                return $form->field($data, 'paymentAmount')->textInput([
                    'value' => round($data->getPaidAmount($model->id), 2),
                    'class' => 'form-control text-right payment-amount',
                    'id' => 'invoice-payment-' . $data->id
                ])->label(false);
            },
            'attribute' => 'new_activity',
            'format' => 'raw'
        ]);
    }
?>
<?php ActiveForm::end(); ?>

<?php Pjax::Begin(['id' => 'invoice-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'invoice-grid',
        'dataProvider' => $invoiceDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'line-items-value invoice-line-items'],
        'emptyText' => 'No Invoices Available!'
    ]); ?>
<?php Pjax::end(); ?>