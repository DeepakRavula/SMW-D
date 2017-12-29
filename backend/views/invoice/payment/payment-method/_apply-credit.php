<?php
use common\models\Invoice;
use yii\grid\GridView;
use common\models\Payment;
use common\models\ItemType;
use yii\data\ArrayDataProvider;
use yii\bootstrap\Modal;

?>
<?php
$invoiceCredits = Invoice::find()
        ->invoiceCredit($invoice->user_id)
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
        } else if((int) $invoiceCredit->isLessonCredit()) {
			$source = 'Lesson Credit';
            $type = 'invoice';	
		}else {
            $source = 'Invoice';
            $type = 'invoice';
        }
		$paymentDate = new \DateTime();
		if(!empty($lastInvoicePayment)) {
	        $paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s', $lastInvoicePayment->date);
		}
        $results[] = [
            'id' => $invoiceCredit->id,
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
        'attributes' => ['id', 'date', 'amount', 'source'],
    ],
]);
?>
<?php if ($creditDataProvider->totalCount > 0):?>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Apply Credit</h4>',
    'id' => 'credit-modal',
    'toggleButton' => ['label' => 'click me', 'class' => 'hide'],
]);

echo GridView::widget([
    'dataProvider' => $creditDataProvider,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'summary' => false,
    'emptyText' => false,
    'rowOptions' => function ($model, $key, $index, $grid) {
        return [
            'data-amount' => $model['amount'],
            'data-id' => $model['id'],
            'data-source' => $model['type'],
        ];
    },
    'columns' => [
        [
        'label' => 'Id',
        'value' => 'id',
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
        'format' => 'decimal',
        'label' => 'Credit',
        'value' => 'amount',
        ],
    ],
]);
 echo $this->render('_form-credit', [
        'model' => new Payment(),
        'invoice' => $invoice,
]);
Modal::end();
?>
<?php endif; ?>
