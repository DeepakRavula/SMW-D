<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use common\models\Invoice;
use common\models\InvoiceLineItem;
?>
<?php
	$results = [];
	$locationId = Yii::$app->session->get('location_id');
	$pendingInvoices = Invoice::find()->alias('i')
			->joinWith(['user' => function($query) use($model){
			$query->joinWith('student s')
				->where(['s.id' => $model->id]);
			}])
			->joinWith(['invoicePayments ip' => function($query){
				$query->where(['ip.id' => null]);
			}])
			->where(['i.type' => Invoice::TYPE_INVOICE, 'i.user_id' => $model->customer->id])
			->all();
			
	if( ! empty($pendingInvoices)){
		foreach($pendingInvoices as $pendingInvoice){
			$lineItem = InvoiceLineItem::findOne(['invoice_id' => $pendingInvoice->id]);
			$results[] = [
				'invoiceNumber' => $pendingInvoice->getInvoiceNumber(),
				'description' => $lineItem->description,
				'total' => $pendingInvoice->total,
				'pending' => $pendingInvoice->invoiceBalance,
			];
		}
	}

	$invoices = Invoice::find()->alias('i')
		->joinWith(['user' => function($query) use($model){
			$query->joinWith('student s')
				->where(['s.id' => $model->id]);
		}])
		->joinWith(['invoicePayments ip' => function($query){
			$query->innerjoinWith(['payment p' => function($query){
			}]);
		}])
	->where(['i.type' => Invoice::TYPE_INVOICE, 'i.user_id' => $model->customer->id])
	->all();
	if(! empty($invoices)){
		foreach($invoices as $invoice){
			if($invoice->total > $invoice->invoicePaymentTotal){
				$invoiceDate = \DateTime::createFromFormat('Y-m-d H:i:s',$invoice->date);
				$lineItem = InvoiceLineItem::findOne(['invoice_id' => $invoice->id]);
		$results[] = [
			'invoiceNumber' => $invoice->getInvoiceNumber(),
			'description' => $lineItem->description,
			'total' => $invoice->total,
			'pending' => $invoice->invoiceBalance,
		];
			}
		}
	}
	$invoiceDataProvider = new ArrayDataProvider([
    'allModels' => $results,
    'sort' => [
        'attributes' => ['invoiceNumber', 'description', 'total','pending'],
    ],
]);
?>
<?php
echo GridView::widget([
	'dataProvider' => $invoiceDataProvider,
	'tableOptions' =>['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray' ],
    'options' => ['class' => 'p-10'],
	'columns' => [
		[
		'label' => 'Invoice Number', 
		'value' => 'invoiceNumber',
		],
		[
		'label' => 'Description',
		'value' => 'description',
		],
		[
		'label' => 'Total', 
		'value' => 'total',
		],
		[
		'label' => 'Pending', 
		'value' => 'pending',
		],
    ]
]);
?>