<?php

use yii\grid\GridView;

?>
<?php yii\widgets\Pjax::begin(['id' => 'payment-index']); ?>
<?php

echo GridView::widget([
    'dataProvider' => $paymentsDataProvider,
    'tableOptions' => ['class' => 'table  m-0 table-more-condensed inner-payment-table'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'summary' => false,
        'emptyText' => false,
    'columns' => [
        [
            'value' => function ($data) {
                return $data->paymentMethod->name;
            },
            'contentOptions' => ['class' => 'text-left'],
        ],
        [
            'value' => function ($data) {
                return !empty($data->reference) ? $data->reference : null;
            },
        ],
        [
            
            'value' => function ($data) {
                return $data->invoice->getInvoicePaymentMethodTotal($data->payment_method_id);
            },
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:80px;'],
        ],
    ],
]);
?>
<?php yii\widgets\Pjax::end(); ?>