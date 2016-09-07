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
	$pendingInvoices = Invoice::find()
			->privateLessonInvoices($model->id, $locationId, $model->customer->id)
			->joinWith(['invoicePayments ip' => function($query){
				$query->where(['ip.id' => null]);
			}])
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