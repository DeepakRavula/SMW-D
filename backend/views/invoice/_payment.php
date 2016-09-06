<?php
use yii\grid\GridView;
use common\models\Payment;
use common\models\InvoicePayment;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use common\models\ItemType;
use common\models\PaymentMethod;
use common\models\PaymentCheque;
use yii\bootstrap\ButtonGroup;
use yii\data\ArrayDataProvider;
?>
<?php
$creditPayments = Payment::find()
		->innerJoinWith('creditUsage cu')
		->joinWith(['invoicePayment ip' => function($query) use($model){
			$query->where(['ip.invoice_id' => $model->id]);
		}])
		->all();

$results = [];
if(! empty($creditPayments)){
	foreach($creditPayments as $creditPayment){
		$debitInvoice = InvoicePayment::findOne(['payment_id' => $creditPayment->creditUsage->debit_payment_id]);
		$invoiceNumber = null;
		if(! empty($debitInvoice)){
			if((int) $debitInvoice->invoice_id === Payment::TYPE_OPENING_BALANCE_CREDIT){
				$invoiceNumber = 'NA';	
			}else{
				$invoiceNumber = $debitInvoice->invoice_id;
			}
		}
		$paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s',$creditPayment->date);
		$results[] = [
			'date' => $paymentDate->format('d-m-Y'),
			'paymentMethodName' => $creditPayment->paymentMethod->name,
			'invoiceNumber' => $invoiceNumber,
			'amount' => $creditPayment->amount,
		];
	}
}

$debitPayments = Payment::find()
		->innerJoinWith('debitUsage du')
		->joinWith(['invoicePayment ip' => function($query) use($model){
			$query->where(['ip.invoice_id' => $model->id]);
		}])
		->all();

if(! empty($debitPayments)){
	foreach($debitPayments as $debitPayment){
		$creditInvoice = InvoicePayment::findOne(['payment_id' => $debitPayment->debitUsage->credit_payment_id]);
		$paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s',$debitPayment->date);
		$results[] = [
			'date' => $paymentDate->format('d-m-Y'),
			'paymentMethodName' => $debitPayment->paymentMethod->name,
			'invoiceNumber' => $creditInvoice->invoice_id,
			'amount' => $debitPayment->amount,
		];
	}
}

$otherPayments = Payment::find()
		->joinWith(['invoicePayment ip' => function($query) use($model){
			$query->where(['ip.invoice_id' => $model->id]);
		}])
		->where(['not in','payment_method_id',[PaymentMethod::TYPE_CREDIT_APPLIED, PaymentMethod::TYPE_CREDIT_USED]])
		->all();

if(! empty($otherPayments)){
	foreach($otherPayments as $otherPayment){
		$paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s',$otherPayment->date);
		$invoiceNumber = 'NA';
		if((int) $otherPayment->payment_method_id === PaymentMethod::TYPE_CHEQUE){
			$chequeModel = PaymentCheque::findOne(['payment_id' => $otherPayment->paymentCheque->payment_id]);	
			$invoiceNumber = $chequeModel->number;
		}
		$results[] = [
			'date' => $paymentDate->format('d-m-Y'),
			'paymentMethodName' => $otherPayment->paymentMethod->name,
			'invoiceNumber' => $invoiceNumber,
			'amount' => $otherPayment->amount,
		];
	}
}

usort($results, function ($item1, $item2) {
	$item1 = new \DateTime($item1['date']);
	$item2 = new \DateTime($item2['date']);
    if ($item1 == $item2) return 0;
    return $item1 < $item2 ? 1 : -1;
});
?>
<?php
$invoicePaymentDataProvider = new ArrayDataProvider([
    'allModels' => $results,
    'sort' => [
        'attributes' => ['date', 'paymentMethodName', 'amount', 'invoiceNumber'],
    ],
]);
?>
<?php
echo GridView::widget([
	'dataProvider' => $invoicePaymentDataProvider,
	'tableOptions' =>['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray' ],
    'options' => ['class' => 'p-10'],
	'columns' => [
		[
		'label' => 'Date', 
		'value' => 'date',
		],
		[
		'label' => 'Payment Method',
		'value' => 'paymentMethodName',
		],
		[
		'label' => 'Number', 
		'value' => 'invoiceNumber',
		],
		[
		'label' => 'Amount', 
		'value' => 'amount',
		],
    ]
]);
?>
<?php if((int) $model->type === Invoice::TYPE_INVOICE):?>
	<div class="smw-box col-md-3 m-l-10 m-b-20">
<h5>Invoice Total: <?= $model->total;?></h5>
<h5>Invoice Paid: <?= $model->invoicePaymentTotal;?></h5>
<h5>Invoice Balance: <?= $model->invoiceBalance;?></h5>
</div>
<div class="clearfix"></div>
<?php endif;?>
<?php $buttons = [];
	$lineItemModel = InvoiceLineItem::findOne(['invoice_id' => $model->id, 'item_type_id' => ItemType::TYPE_GROUP_LESSON]);
?>
<?php foreach(PaymentMethod::findAll([
			'active' => PaymentMethod::STATUS_ACTIVE,
			'displayed' => 1,
		]) as $method):?>
	<?php if((int) $model->type === Invoice::TYPE_PRO_FORMA_INVOICE || ! empty($lineItemModel->item_type_id)):?>
	<?php if($method->name === 'Apply Credit'):?>
	<?php continue;?>
	<?php endif;?>
	<?php endif;?>
	<?php $buttons[] = [
			'label' => $method->name, 
			'options' => [
				'class' => 'btn btn-outline-info',
				'id' => str_replace(' ', '-', trim(strtolower($method->name))) . '-btn',
				'data-payment-type' => str_replace(' ', '-', trim(strtolower($method->name))),
				'data-payment-type-id' => $method->id,
			],
	];?>
<?php endforeach;?>

<?php // a button group with items configuration
echo ButtonGroup::widget([
    'buttons' => $buttons,
	'options' => [
		'id' => 'payment-method-btn-section',
		'class' => 'btn-group-horizontal p-l-10'
	]
]);?>


<?php foreach(PaymentMethod::findAll([
			'active' => PaymentMethod::STATUS_ACTIVE,
			'displayed' => 1,
			'id' => [4,5,6,7],
		]) as $method):?>
	<div id="<?= str_replace(' ', '-', trim(strtolower($method->name))) . '-section';?>" class="payment-method-section" style="display: none;">
		<?php echo $this->render('payment-method/_' . str_replace(' ', '-', trim(strtolower($method->name))),[
				'model' => new Payment(),
				'invoice' => $model,
				'chequeModel' => new PaymentCheque(),
		]);?>	
	</div>
	<?php endforeach;?>

<script type="text/javascript">
$(document).ready(function(){
  $('#payment-method-btn-section').on('click', '.btn', function() {
	 $('.payment-method-section').hide();
	 $('#' + $(this).data('payment-type') + '-section').show();
	 $('.payment-method-id').val($(this).data('payment-type-id'));
     $('#payment-method-btn-section .btn').removeClass('active');
     $(this).addClass('active');
     if($(this).data('payment-type') == 'apply-credit'){
         $('#credit-modal').modal('show');
     }
  });
  $('td').click(function () {
        var amount = $(this).closest('tr').data('amount');
        var id = $(this).closest('tr').data('id');
        var type = $(this).closest('tr').data('source');
        $('#payment-credit').val(amount);
		$('#payment-sourceid').val(id);
		$('#payment-sourcetype').val(type);
    });
});
</script>