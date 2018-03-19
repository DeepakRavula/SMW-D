<?php
use common\models\Invoice;
use yii\grid\GridView;
use common\models\Payment;
use common\models\ItemType;
use yii\data\ArrayDataProvider;

?>

<h5><strong>Choose the credit that you wish to apply</strong></h5>
<?php
echo GridView::widget([
    'dataProvider' => $creditDataProvider,
    'tableOptions' => ['class' => 'table table-bordered'],
    'id' => 'apply-credit-grid',
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'summary' => false,
    'emptyText' => false,
    'rowOptions' => function ($model, $key, $index, $grid) {
        return [
            'data-amount' => $model['amount'],
            'data-id' => $model['id'],
            'data-number' => $model['invoice_number'],
        ];
    },
    'columns' => [
        [
        'contentOptions' => ['class' => 'text-left','style' => 'min-width:60%;max-width:30%;'],
        'headerOptions' => ['class' => 'text-left','style' => 'min-width:60%;max-width:30%;'],
        'label' => 'Invoice Number',
        'value' => 'invoice_number',
        ],
        [
        'contentOptions' => ['class' => 'text-center','style' => 'min-width:40%;max-width:40%;'],
        'headerOptions' => ['class' => 'text-center','style' => 'min-width:40%;max-width:40%;'],
        'label' => 'Date',
        'value' => 'date',
        ],
        [
        'contentOptions' => ['class' => 'text-right','style' => 'min-width:30%;max-width:30%;'],
        'headerOptions' => ['class' => 'text-right','style' => 'min-width:30%;max-width:30%;'],
        'label' => 'Credit',
        'value' => function ($model) {
            return Yii::$app->formatter->asDecimal($model['amount'], 2);
        }
        ],
    ]
]);
    echo $this->render('_form-credit', [
            'model' => $paymentModel,
            'invoice' => $invoice,
    ]);
?>
