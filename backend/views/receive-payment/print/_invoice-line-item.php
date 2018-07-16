<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;

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
            return !empty($data->total) ? Yii::$app->formatter->asCurrency($data->total) : null;
        }
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right invoice-value'],
        'label' => 'Balance',
        'value' => function ($data) {
            return !empty($data->balance) ? Yii::$app->formatter->asCurrency($data->balance) : null;
        }
    ]);

if ($searchModel->showCheckBox && !$isCreatePfi) {
    array_push($columns, [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Payment',
        'value' => function ($data) { 
            return Html::label('', round($data->balance, 2), ['class' => 'payment-amount text-right']); 
        },
        'attribute' => 'new_activity',
        'format' => 'raw',
    ]);
}
?>

<?php Pjax::Begin(['id' => 'invoice-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'invoice-line-item-grid',
        'dataProvider' => $invoiceLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'line-items-value invoice-line-items'],
        'emptyText' => 'No Invoices Available!'
    ]); ?>
<?php Pjax::end(); ?>

