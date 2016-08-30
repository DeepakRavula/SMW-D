<?php
use common\models\Invoice;
use yii\grid\GridView;
use common\models\Payment;
use common\models\PaymentMethod;
use yii\data\ArrayDataProvider;
use yii\bootstrap\Modal;

?>
<?php
$invoiceCredits = Invoice::find()
		->invoiceCredit($invoice->user_id)
		->all();

$results = [];
if(! empty($invoiceCredits )){
	foreach($invoiceCredits as $invoiceCredit){
		$lastInvoicePayment = $invoiceCredit->invoicePayments;
		$lastInvoicePayment = end($lastInvoicePayment);
		$paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s',$lastInvoicePayment->payment->date);
		$results[] = [
			'id' => $invoiceCredit->id,
			'date' => $paymentDate->format('d-m-Y'),
			'amount' => abs($invoiceCredit->balance),
			'source' => 'Invoice',
			'type' => 'invoice'
		];
	}
}
$openingBalancePaymentModel = Payment::find()
				->where([
					'user_id' => $invoice->user_id,
					'payment_method_id' => [PaymentMethod::TYPE_ACCOUNT_ENTRY, ],
			])->one();
	
		$remainingOpeningBalance = 0;
		if(! empty($openingBalancePaymentModel->id)){
			$openingBalanceCreditsUsed = Payment::find()
					->joinWith(['invoicePayment ip' => function($query) use($model){
						$query->where(['ip.invoice_id' => Payment::TYPE_OPENING_BALANCE_CREDIT]);	
					}])
					->where(['user_id' => $invoice->user_id])
					->sum('amount');

			$remainingOpeningBalance = $openingBalancePaymentModel->amount + $openingBalanceCreditsUsed;
		}
		
if($remainingOpeningBalance > 0){
	$paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s',$openingBalancePaymentModel->date);
	$results[] = [
			'id' => $openingBalancePaymentModel->id,
			'date' => $paymentDate->format('d-m-Y'),
			'amount' => abs($remainingOpeningBalance),
			'source' => 'Opening Balance',
			'type' => 'account_entry'
		];
}

$proFormaInvoiceCredits = Invoice::find()->alias('i')
		->select(['i.id', 'i.date', 'SUM(p.amount) as credit'])
		->joinWith(['invoicePayments ip' => function($query){
			$query->joinWith(['payment p' => function($query){
			}]);
		}])
		->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE, 'i.user_id' => $invoice->user_id])
		->groupBy('i.id')
		->all();
		
foreach($proFormaInvoiceCredits as $proFormaInvoiceCredit){
	if($proFormaInvoiceCredit->credit <= 0){
		continue;
	}
	$paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s',$proFormaInvoiceCredit->date);
	$results[] = [
		'id' => $proFormaInvoiceCredit->id,
		'date' => $paymentDate->format('d-m-Y'),
		'amount' => $proFormaInvoiceCredit->credit,
		'source' => 'Pro-forma Invoice',
		'type' => 'pro_forma_invoice'
	];
}

$creditDataProvider = new ArrayDataProvider([
    'allModels' => $results,
    'sort' => [
        'attributes' => ['id', 'date', 'amount', 'source'],
    ],
]);
?>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Apply Credit</h4>',
    'id'=>'credit-modal',
    'toggleButton' => ['label' => 'click me', 'class' => 'hide'],
]);

echo GridView::widget([
	'dataProvider' => $creditDataProvider,
	'tableOptions' =>['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray' ],
    'rowOptions'   => function ($model, $key, $index, $grid) {
        return [
			'data-amount' => $model['amount'], 
			'data-id' => $model['id'],
			'data-source' => $model['type']
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
		'label' => 'Credit',
		'value' => 'amount',
		]
    ]
]);

Modal::end();
?>
<?php echo $this->render('_form-credit', [
		'model' => new Payment(),
]) ?>

