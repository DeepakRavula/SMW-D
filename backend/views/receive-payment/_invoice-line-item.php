<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use yii\helpers\Url;
use common\models\Invoice;
use yii\bootstrap\ActiveForm;

?>

<?php 
    $form = ActiveForm::begin([
        'id' => 'modal-form-invoice',
        'enableClientValidation' => false
    ]);
?>

<?php
    $columns = [];
    if ($searchModel->showCheckBox) {
        $contentWidth   =   "width:0px;";
        array_push($columns, [
            'class' => 'yii\grid\CheckboxColumn',
            'contentOptions' => ['style' => 'width:30px;'],
            'checkboxOptions' => function($model, $key, $index, $column) {
                return ['checked' => true, 'class' =>'check-checkbox'];
            }
        ]);
    }
    else{
        $contentWidth   =   "width:650px;";
    }

    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Date',
        'value' => function ($data) {
            return  !empty($data->date) ? Yii::$app->formatter->asDate($data->date): null;
        }
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-left','style' => $contentWidth],
        'contentOptions' => ['class' => 'text-left','style' => $contentWidth],
        'label' => 'Number',
        'value' => function ($data) {
            return $data->invoiceNumber;
        }
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Amount',
        'value' => function ($data) {
            return !empty($data->total) ? Yii::$app->formatter->asCurrency(round($data->total, 2)) : null;
        }
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right invoice-value'],
        'label' => 'Balance',
        'value' => function ($data) {
            return !empty($data->balance) ? (round($data->balance, 2) > 0.00 && round($data->balance, 2) <= 0.09) || (round($data->balance, 2) < 0.00 && round($data->balance, 2) >= -0.09) ? Yii::$app->formatter->asCurrency(round(0.00, 2)):Yii::$app->formatter->asCurrency(round($data->balance, 2)) : null;
        }
    ]);
    if (isset($changeGridId)) {
        array_push($columns, [
            'label' => 'Status',
            'value' => function ($data) {
                return $data->getStatus();
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right invoice-value']
        ]); 
    }


if ($searchModel->showCheckBox && !$isCreatePfi) {
    array_push($columns, [
        'headerOptions' => ['class' => 'text-right', 'style' => 'width:180px;text-align:right'],
        'contentOptions' => ['class' => 'text-right', 'style' => 'width:180px;text-align:right'],
        'label' => 'Payment',
        'value' => function ($data) use ($form) {
            return $form->field($data, 'paymentAmount')->textInput([
                'value' => Yii::$app->formatter->asCurrency($data->balance), 
                'class' => 'form-control text-right payment-amount',
                'id' => 'invoice-payment-' . $data->id,
                'invoiceId' => $data->id
            ])->label(false);
        },
        'attribute' => 'new_activity',
        'format' => 'raw',
    ]);
}
?>

<?php $gridId = 'invoice-line-item-grid'; $pjaxId = 'invoice-line-item-listing'; $class = 'line-items-value invoice-line-items'; ?>
<?php if (isset($changeGridId)) {
    $gridId = 'invoice-line-item-grid-pr';
    $pjaxId = 'invoice-line-item-listing-pr';
    $class = 'line-items-value-pr invoice-line-items-pr';
} ?>
<?php Pjax::Begin(['id' => $pjaxId, 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => $gridId,
        'dataProvider' => $invoiceLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => $class],
        'emptyText' => 'No Invoices Available!'
    ]); ?>
<?php Pjax::end(); ?>

<?php ActiveForm::end();