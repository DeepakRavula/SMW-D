<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;

?>
<?php
    $columns = [];
    if ($searchModel->showCheckBox) {
        $contentWidth   =   "width:0px;";
    }
    else{
        $contentWidth   =   "width:650px;";
    }

    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Date',
        'value' => 'date'
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-left','style' => $contentWidth],
        'contentOptions' => ['class' => 'text-left','style' => $contentWidth],
        'label' => 'Number',
        'value' => 'number'
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Amount',
        'value' => 'amount'
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Payment',
        'value' => 'payment'
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right invoice-value'],
        'label' => 'Balance',
        'value' => 'balance'
    ]);
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

