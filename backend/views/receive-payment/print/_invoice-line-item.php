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
            return !empty($data->balance) ?(round($data->balance, 2) > 0.00 && round($data->balance, 2) <= 0.09) || (round($data->balance, 2) < 0.00 && round($data->balance, 2) >= -0.09)  ? Yii::$app->formatter->asCurrency(round('0.00', 2)): Yii::$app->formatter->asCurrency(round($model->balance, 2)) : null;
        }
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

