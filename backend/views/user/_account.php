<?php

use yii\grid\GridView;
use common\models\Invoice;
use common\models\Payment;
use common\models\PaymentMethod;
use yii\data\ArrayDataProvider;
use yii\bootstrap\Modal;

?>
<?php
$invoiceCredits = Invoice::find()
		->invoiceCredit($model->id)
		->all();

$results = [];
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

$openingBalancePaymentModel = Payment::find()
				->where([
					'user_id' => $model->id,
					'payment_method_id' => [PaymentMethod::TYPE_ACCOUNT_ENTRY, ],
			])->one();
	
		$remainingOpeningBalance = 0;
		if(! empty($openingBalancePaymentModel->id)){
			$openingBalanceCreditsUsed = Payment::find()
					->joinWith(['invoicePayment ip' => function($query) use($model){
						$query->where(['ip.invoice_id' => Payment::TYPE_OPENING_BALANCE_CREDIT]);	
					}])
					->where(['user_id' => $model->id])
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
		->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE, 'i.user_id' => $model->id])
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

$newInvoices = Invoice::find()
		->joinWith(['invoicePayments ip' => function($query){
			$query->where(['ip.id' => null]);
		}])
		->where(['user_id' => $model->id])
		->all();

	foreach($newInvoices as $newInvoice){
	$invoiceDate = \DateTime::createFromFormat('Y-m-d H:i:s',$newInvoice->date);
		$results[] = [
			'id' => $newInvoice->id,
			'date' => $invoiceDate->format('d-m-Y'),
			'amount' => $newInvoice->total,
			'source' => 'Invoice',
			'type' => 'invoice'
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
echo GridView::widget([
	'dataProvider' => $creditDataProvider,
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
?>