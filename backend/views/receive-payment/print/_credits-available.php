<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use common\models\Invoice;
use common\models\Payment;
use yii\bootstrap\ActiveForm;
?>

<?php 
    $form = ActiveForm::begin([
        'id' => 'modal-form-credit',
        'enableClientValidation' => false
    ]);
?>

<?php
    $columns = [];
    
    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => function ($model) {
            return [
                'creditId' => $model['id'],
                'class' => 'text-left credit-type'
            ];
        },
        'label' => 'Type',
        'value' => 'type',
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Reference',
        'value' => 'reference',
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-left', 'style' => 'width:180px'],
        'contentOptions' => ['class' => 'text-left', 'style' => 'width:180px'],
        'label' => 'Payment Method',
        'value' => 'method',
        'format' => 'raw'
    ]);    

    array_push($columns, [
        'format' => 'currency',
        'headerOptions' => ['class' => 'text-right', 'style' => 'text-align:right'],
        'contentOptions' => ['class' => 'text-right credit-value', 'style' => 'text-align:right'],
        'label' => 'Amount',
        'value' => 'amount'
    ]);

    array_push($columns, [
        'format' => 'currency',
        'headerOptions' => ['class' => 'text-right', 'style' => 'width:180px;text-align:right;'],
        'contentOptions' => ['class' => 'text-right', 'style' => 'width:180px;text-align:right'],
        'label' => 'Amount Used',
        'value' => 'amountUsed',
    ]);
?>

<?php Pjax::Begin(['id' => 'credit-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'credit-line-item-grid',
        'dataProvider' => $paymentLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'credit-items-value'],
        'emptyText' => 'No Credits Available!'
    ]); ?>
<?php Pjax::end(); ?>

<?php ActiveForm::end();