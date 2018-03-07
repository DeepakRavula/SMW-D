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
            'data-source' => $model['type'],
            'data-number' => $model['invoice_number'],
        ];
    },
    'columns' => [
        [
        'label' => 'Invoice Number',
        'value' => 'invoice_number',
        ],
        [
        'label' => 'Source',
        'value' => 'source',
        ],
        [
        'label' => 'Date',
        'value' => 'date',
        ],
        [
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
