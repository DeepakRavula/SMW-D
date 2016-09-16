<?php

use yii\grid\GridView;
use common\models\Invoice;
use yii\data\ArrayDataProvider;
use common\models\InvoiceLineItem;
use common\models\ItemType;
?>
<?php
$invoiceCredits = Invoice::find()
	->invoiceCredit($model->id)
	->all();

$results = [];
foreach($invoiceCredits as $invoiceCredit){
$lastInvoicePayment = $invoiceCredit->invoicePayments;
$lastInvoicePayment = end($lastInvoicePayment);
$invoiceLineItem = InvoiceLineItem::findOne(['invoice_id' => $invoiceCredit->id]);
if((int) $invoiceLineItem->item_type_id === (int) ItemType::TYPE_OPENING_BALANCE){
	$source = 'Opening Balance Credit';
} else {
	$source = 'Invoice Credit';
}
$paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s',$lastInvoicePayment->payment->date);
$results[] = [
	'id' => $invoiceCredit->getInvoiceNumber(),
	'date' => $paymentDate->format('d-m-Y'),
	'total' => $invoiceCredit->total,
	'paid' => $invoiceCredit->invoicePaymentTotal,
	'owing' => -abs($invoiceCredit->invoiceBalance),
	'source' => $source,
];
}

$proFormaInvoiceCredits = Invoice::find()->alias('i')
	->select(['i.id', 'i.date', 'SUM(p.amount) as credit', 'total'])
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
		'id' => $proFormaInvoiceCredit->getInvoiceNumber(),
		'date' => $paymentDate->format('d-m-Y'),
		'total' => $proFormaInvoiceCredit->total,
		'paid' => $proFormaInvoiceCredit->total,
		'owing' => -abs($proFormaInvoiceCredit->credit),
		'source' => 'Pro-forma Invoice',
	];
}

$newInvoices = Invoice::find()
	->joinWith(['invoicePayments ip' => function($query){
		$query->where(['ip.id' => null]);
	}])
	->where(['user_id' => $model->id, 'type' => Invoice::TYPE_INVOICE])
	->all();
	
foreach($newInvoices as $newInvoice){
	$invoiceDate = \DateTime::createFromFormat('Y-m-d H:i:s',$newInvoice->date);
		$results[] = [
			'id' => $newInvoice->getInvoiceNumber(),
			'date' => $invoiceDate->format('d-m-Y'),
			'total' => $newInvoice->total,
			'paid' => $newInvoice->invoicePaymentTotal,
			'owing' => $newInvoice->invoiceBalance,
			'source' => 'Invoice',
		];	
}

$invoices = Invoice::find()->alias('i')
->joinWith(['invoicePayments ip' => function($query){
	$query->innerjoinWith(['payment p' => function($query){
	}]);
}])
->where(['i.type' => Invoice::TYPE_INVOICE, 'i.user_id' => $model->id])
->all();
if(! empty($invoices)){
	foreach($invoices as $invoice){
		if($invoice->total > $invoice->invoicePaymentTotal){
			$invoiceDate = \DateTime::createFromFormat('Y-m-d H:i:s',$invoice->date);
	$results[] = [
		'id' => $invoice->getInvoiceNumber(),
		'date' => $invoiceDate->format('d-m-Y'),
		'total' => $invoice->total,
		'paid' => $invoice->invoicePaymentTotal,
		'owing' => $invoice->invoiceBalance,
		'source' => 'Invoice',
	];
		}
	}
}
$creditDataProvider = new ArrayDataProvider([
'allModels' => $results,
'sort' => [
	'attributes' => ['id', 'date', 'total', 'source','paid','owing'],
],
]);
?>
<?php yii\widgets\Pjax::begin([
	'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
'dataProvider' => $creditDataProvider,
'tableOptions' =>['class' => 'table table-bordered m-0'],
'headerRowOptions' => ['class' => 'bg-light-gray' ],
'rowOptions'   => function ($model, $key, $index, $grid) {
	return [
		'data-id' => $model['id'],
	];
},
'columns' => [
	[
	'label' => 'Invoice Number', 
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
	'label' => 'Total',
	'value' => 'total',
	],
	[
	'label' => 'Paid',
	'value' => 'paid',
	],
	[
	'label' => 'Owing',
	'value' => 'owing',
	]
]
]);
?>
<?php \yii\widgets\Pjax::end(); ?>