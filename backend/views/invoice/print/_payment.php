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
        'label' => 'Date',
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->date);
        },
        ],
    [
        'label' => 'Type',
        'value' => function ($data) {
            return $data->paymentMethod->name;
        },
        ],
    [
        'label' => 'Ref',
        'value' => function ($data) {
            return $data->reference;
        },
        ],
    [
        'contentOptions' => ['class' => 'text-left payment-notes-description', 'style' => 'width:125px;'],
        'label' => 'Notes',
        'value' => function ($data) {
            return $data->notes;

        },
        ],
        [
            'label' => 'Amount',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->amount);
            },
        ],
    ],
]);
?>
<?php yii\widgets\Pjax::end(); ?>