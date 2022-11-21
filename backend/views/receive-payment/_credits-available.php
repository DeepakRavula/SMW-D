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
        'class' => 'yii\grid\CheckboxColumn',
        'contentOptions' => ['style' => 'width:30px;'],
        'checkboxOptions' => function($model, $key, $index, $column) {
            return ['checked' => true, 'class' =>'check-checkbox'];
        }
    ]);

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
        'format' => 'currency',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right credit-value'],
        'label' => 'Amount',
        'value' => 'amount'
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-right', 'style' => 'width:180px'],
        'contentOptions' => ['class' => 'text-right', 'style' => 'width:180px'],
        'label' => 'Payment',
        'value' => function ($model) use ($form) {
            if ($model['type'] == 'Payment Credit') {
                $fieldModel = Payment::findOne($model['id']);
            } else {
                $fieldModel = Invoice::findOne($model['id']);
            }
            return $form->field($fieldModel, 'paymentAmount')->textInput([
                'value' => round($model['amount'], 2),
                'class' => 'form-control text-right credit-amount',
                'id' => 'credit-payment-' . $model['id'],
                'creditId' => $model['id']
            ])->label(false);
        },
        'format' => 'raw'
    ]);
?>

<?php Pjax::Begin(['id' => 'credit-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'credit-line-item-grid',
        'dataProvider' => $creditDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'credit-items-value'],
        'emptyText' => 'No Credits Available!'
    ]); ?>
<?php Pjax::end(); ?>

<?php ActiveForm::end();