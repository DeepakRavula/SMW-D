<?php

use yii\grid\GridView;

?>
<style>
    @media print{
        .invoice-notes-column-width
        {
            width:155px;
        }
        .invoice-other-column-width
        {
            width:65px;
        }
    }
</style>
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
        'contentOptions' => ['class' => 'invoice-other-column-width'],
        'label' => 'Date',
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->date);
        },
        ],
    [
         'contentOptions' => ['class' => 'invoice-other-column-width'],
        'label' => 'Type',
        'value' => function ($data) {
            return $data->paymentMethod->name;
        },
        ],
    [
        'contentOptions' => ['class' => 'invoice-other-column-width'],
        'label' => 'Ref',
        'value' => function ($data) {
            return $data->reference;
        },
        ],
    [
        'contentOptions' => ['class' => 'text-left invoice-notes-column-width'],
        'label' => 'Notes',
        'value' => function ($data) {
            return $data->notes;

        },
        ],
        [
            'label' => 'Amount',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right invoice-other-column-width'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->amount);
            },
        ],
    ],
]);
?>
<?php yii\widgets\Pjax::end(); ?>