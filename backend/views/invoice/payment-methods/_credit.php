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
$invoiceCredits = Invoice::find()
		->invoiceCredits($invoice->user_id)
		->all();

$results = [];
foreach($invoiceCredits as $invoiceCredit){
	$lastInvoicePayment = $invoiceCredit->invoicePayments;
	$lastInvoicePayment = end($lastInvoicePayment);
	$paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s',$lastInvoicePayment->payment->date);
	$results[] = [
		'id' => $invoiceCredit->id,
		'date' => $paymentDate->format('d-m-Y'),
		'amount' => abs($invoiceCredit->balance)
	];
}
$proFormaInvoiceCredits = Invoice::find()->alias('i')
		->joinWith(['invoicePayments ip' => function($query){
			$query->joinWith(['payment p' => function($query){
			}]);
		}])
		->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE, 'i.user_id' => $invoice->user_id])
		->groupBy('i.id')
		->all();

		
foreach($proFormaInvoiceCredits as $proFormaInvoiceCredit){
	$results[] = [
		'id' => $proFormaInvoiceCredit->id,
		'date' => $proFormaInvoiceCredit->invoicePayments->payment->date,
		'amount' => $proFormaInvoiceCredit
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
