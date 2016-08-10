<?php
use common\models\Invoice;
use yii\grid\GridView;
use common\models\Payment;
use common\models\PaymentMethod;
use yii\data\ArrayDataProvider;
use yii\bootstrap\Modal;

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
<?php
Modal::begin([
    'header' => '<h2>Apply Credit</h2>',
    'id'=>'credit-modal',
    'toggleButton' => ['label' => 'click me', 'class' => 'hide'],
]);

echo GridView::widget([
	'dataProvider' => $creditDataProvider,
    'rowOptions'   => function ($model, $key, $index, $grid) {
        return ['data-amount' => $model['amount']];
    },
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

Modal::end();
?>
<?php echo $this->render('_form-credit', [
		'model' => new Payment(),
]) ?>

<?php
$this->registerJs("
    $('td').click(function () {
        var amount = $(this).closest('tr').data('amount');
        $('#payment-credit').val(amount);
        $('#credit-modal').modal('hide');
    });

");
?>
