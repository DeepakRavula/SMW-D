<?php
use common\models\Invoice;
use yii\grid\GridView;
use common\models\Payment;
use common\models\ItemType;
use yii\data\ArrayDataProvider;

?>
<?php
$invoiceCredits = Invoice::find()
        ->invoiceCredit($invoice->user_id)
        ->andWhere(['NOT', ['invoice.id' => $invoice->id]])
        ->all();

$results = [];
if (!empty($invoiceCredits)) {
    foreach ($invoiceCredits as $invoiceCredit) {
        if ($invoiceCredit->isReversedInvoice()) {
            $lastInvoicePayment = $invoiceCredit;
        } else {
            $lastInvoicePayments = $invoiceCredit->payments;
            $lastInvoicePayment = end($lastInvoicePayments);
        }
        $lineItems = $invoiceCredit->lineItems;
        $lineItem = end($lineItems);
        if ((int) $lineItem->item_type_id === (int) ItemType::TYPE_OPENING_BALANCE) {
            $source = 'Opening Balance';
            $type = 'account_entry';
        } elseif ((int) $invoiceCredit->isLessonCredit()) {
            $source = 'Lesson Credit';
            $type = 'invoice';
        } else {
            $source = 'Invoice';
            $type = 'invoice';
        }
        $paymentDate = new \DateTime();
        if (!empty($lastInvoicePayment)) {
            $paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s', $lastInvoicePayment->date);
        }
        $results[] = [
            'id' => $invoiceCredit->id,
            'invoice_number' => $invoiceCredit->getInvoiceNumber(),
            'date' => $paymentDate->format('d-m-Y'),
            'amount' => abs($invoiceCredit->balance),
            'source' => $source,
            'type' => $type,
        ];
    }
}

$creditDataProvider = new ArrayDataProvider([
    'allModels' => $results,
    'sort' => [
        'attributes' => ['id', 'invoice_number', 'date', 'amount', 'source'],
    ],
]);
?>
<?php if ($creditDataProvider->totalCount > 0): ?>
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
    ],
]);
 echo $this->render('_form-credit', [
        'model' => $paymentModel,
        'invoice' => $invoice,
]);
?>
<?php endif; ?>
