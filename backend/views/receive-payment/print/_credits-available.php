<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;

?>

<?php
    $columns = [];
   


    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Date',
        'value' => function ($data) {
            return  !empty($data->date) ? Yii::$app->formatter->asDate($data->date): null;
        }
    ]);
    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Amount',
        'value' => function ($data) {
            return  !empty($data->amount) ? $data->amount: null;
        }
    ]);
    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Payment Method',
        'value' => function ($data) {
            return  !empty($data->paymentMethod->name) ? $data->paymentMethod->name: null;
        }
    ]);
    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Amount Used',
        'value' => function ($data) use($receiptModel){
            return  !empty($data->getAmountUsedInPaymentforTransacation($receiptModel->id,$data->id)) ? $data->getAmountUsedInPaymentforTransacation($receiptModel->id,$data->id): null;
        }
    ]);

?>

<?php Pjax::Begin(['id' => 'credit-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'credit-lesson-line-item-grid',
        'dataProvider' => $paymentLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'credit-items-value'],
        'emptyText' => 'No Credits Available!'
    ]); ?>
<?php Pjax::end(); ?>




