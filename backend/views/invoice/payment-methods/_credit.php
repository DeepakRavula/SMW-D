<?php
use common\models\Invoice;
use yii\grid\GridView;
use common\models\Payment;
use common\models\PaymentMethod;
use yii\data\ArrayDataProvider;

?>
<h3>Apply Credit</h3>
<?php
$creditsQuery = Payment::find()
		->joinWith(['invoicePayment ip' => function($query){
			$query->joinWith(['invoice i' => function($query){
				$query->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);	
			}]);
		}])
		->where(['payment.user_id' => $invoice->user_id])
		->all();
		//->orWhere(['like','payment.amoiunt','-']);
		
$invoiceCreditQuery = Invoice::find()
		->where(['<>','balance',0])
		->all();

$results = [];
foreach($creditsQuery as $credit){
	$results[] = [
		'id' => $credit['id'],
		'date' => $credit['date'],
		'amount' => $credit['amount']
	];
}

foreach($invoiceCreditQuery as $credit){
	$results[] = [
		'id' => $credit['id'],
		'date' => $credit['date'],
		'amount' => $credit['balance']
	];
}

$creditDataProvider = new ArrayDataProvider([
    'allModels' => $results,
    'sort' => [
        'attributes' => ['id', 'date', 'amount'],
    ],
]);
?>
<?php echo GridView::widget([
	'dataProvider' => $creditDataProvider,
	'columns' => [
		[
		'label' => 'Id', 
		'value' => 'id',
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

