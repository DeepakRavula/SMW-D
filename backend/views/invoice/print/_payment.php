<?php

use yii\grid\GridView;

?>
<style>
    @media print{
        .invoice-notes-column-width
        {
            width:155px;
            position:fixed;
        }
        .invoice-other-column-width
        {
            width:65px;
            position:fixed;
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
        'contentOptions' => ['style' =>'max-width:60px'],
        'label' => 'Date',
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->date);
        },
        ],
    [
         'contentOptions' => ['style' =>'max-width:60px'],
        'label' => 'Type',
        'value' => function ($data) {
            return $data->paymentMethod->name;
        },
        ],
    [
        'contentOptions' => ['style' =>'max-width:60px'],
        'label' => 'Ref',
        'value' => function ($data) {
            return $data->reference;
        },
        ],
    [
       'contentOptions' => ['class'=>'text-left','style' => 'max-width:155px;overflow: auto; word-wrap: break-word;'],
        'label' => 'Notes',
        'value' => function ($data) {
            return $data->notes;

        },
        ],
        [
            'label' => 'Amount',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right','style' =>'max-width:60px'],
            'value' => function ($data) {
                return round($data->amount, 2);
            },
        ],
    ],
]);
?>
<?php yii\widgets\Pjax::end(); ?>