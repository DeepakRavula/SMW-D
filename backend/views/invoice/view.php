<?php

use yii\helpers\Html;
use backend\models\search\InvoiceSearch;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;
use common\models\InvoiceLineItem;
use kartik\switchinput\SwitchInput;
use common\models\Note;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Payment;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoice' : 'Invoice';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'InvoiceSearch[type]' => $model->type], ['class' => 'go-back']);
$this->params['action-button'] = $this->render('_buttons', [
	'model' => $model,
]); ?>
<?php if ((int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE): ?>
<?php $this->params['show-all'] = $this->render('_show-all', [
	'model' => $model,
]); ?>
<?php endif; ?>
<div id="invoice-discount-warning" style="display:none;" class="alert-warning alert fade in"></div>
<div class="row">
	<div class="col-md-6">
		<?=
		$this->render('_details', [
			'model' => $model,
		]);
		?>
	</div>
    <?php if (!empty($customer)):?>
	<div class="col-md-6">
		<?=
		$this->render('_customer-details', [
			'model' => $model,
			'customer' => $customer,
			'searchModel' => $searchModel,
		]);
		?>	
	</div>
	<?php endif; ?>
</div>
<?php
$lineItem = InvoiceLineItem::findOne(['invoice_id' => $model->id]);
if (!empty($lineItem)) {
    $itemTypeId = $lineItem->item_type_id;
} else {
    $itemTypeId = null;
}

?>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Email Preview</h4>',
    'id'=>'invoice-mail-modal'
]);
 echo $this->render('mail/preview', [
		'model' => $model,
]);
Modal::end();
?>
<?php if(empty($model->lineItem) || $model->lineItem->isMisc()) : ?>
<div class="nav-tabs-custom">
<?php 

$customerContent = $this->render('_customer', [
    'model' => $model,
    'customer' => $customer,
]);
$guestContent = $this->render('_guest', [
    'model' => $model,
    'userModel' => $userModel,
    'customer' => $customer,
]);
?>
<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Customer',
            'content' => $customerContent,
            'options' => [
                    'id' => 'customer-tab',
                ],
        ],
        [
            'label' => 'Walk-in',
            'content' => $guestContent,
            'options' => [
                    'id' => 'guest-tab',
                ],
        ],
    ],
]); ?>
</div>
<?php endif; ?>
<div class="nav-tabs-custom">
<?php 
$payments = Payment::find()
	->joinWith(['invoicePayments' => function ($query) use($model) {
		$query->where(['invoice_id' => $model->id]);
	}])
	->groupBy('payment.payment_method_id');

$paymentsDataProvider = new ActiveDataProvider([
	'query' => $payments,
]);
$invoiceContent = $this->render('_view-invoice', [
    'model' => $model,
    'customer' => $customer,
    'searchModel' => $searchModel,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
	'paymentsDataProvider' => $paymentsDataProvider
]);
$paymentContent = $this->render('payment/_index', [
    'model' => $model,
    'invoicePayments' => $invoicePayments,
    'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
]);
$noteContent = $this->render('note/view', [
	'model' => new Note(),
	'noteDataProvider' => $noteDataProvider
]);
$logContent = $this->render('log', [
	'model' => $model,
]);

?>
<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Items',
            'content' => $invoiceContent,
            'options' => [
                    'id' => 'invoice',
        ],
        ],
		 [
            'label' => 'Payments',
            'content' => $paymentContent,
            'options' => [
                'id' => 'payment',
        	],
        ],
		[
            'label' => 'Comments',
            'content' => $noteContent,
            'options' => [
                'id' => 'comment',
        ],
        ],
       
		[
            'label' => 'History',
            'content' => $logContent,
            'options' => [
                    'id' => 'log',
        ],
    ],
    ],
]); ?>
</div>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit Line Item</h4>',
    'id' => 'line-item-edit-modal',
]); ?>

<div id="line-item-edit-content"></div>
<?php Modal::end();?>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit Payment</h4>',
    'id' => 'payment-edit-modal',
]); ?>
<div id="payment-edit-content"></div>
<?php Modal::end();?>
<script>
var invoice = {
    onEditableGridSuccess : function(event, val, form, data) {
        invoice.updateSummarySectionAndStatus();
    },
    updateInvoiceStatus : function(status){
        $('#invoice-status').text(status);

    },
    updateSummarySectionAndStatus : function() {
        $.ajax({
            url    : '<?= Url::to(['invoice/fetch-summary-and-status', 'id' => $model->id]) ?>',
            type   : 'GET',
            dataType: "json",
            success: function(response)
            {
                $('#invoice-summary-section').html(response.summary);
                invoice.updateInvoiceStatus(response.status);
                $('#invoice-payment-detail').html(response.details);
            }
        });
        return false;
    }
}
var payment = {
	onEditableGridSuccess :function(event, val, form, data) {
            invoice.updateSummarySectionAndStatus();
        }
}
 $(document).ready(function() {
	 $(document).on('click', '#invoice-note', function (e) {
		$('#note-content').val('');
		$('#invoice-note-modal').modal('show');
		return false;
  	});
	$(document).on('click', '#invoice-mail-button', function (e) {
		$('#invoice-mail-modal').modal('show');
		return false;
  	});
	$(document).on('click', '.apply-credit', function (e) {
		$('#credit-modal').modal('show');
		return false;
  	});
	$(document).on('click', '.add-payment', function (e) {
		$('#payment-modal').modal('show');
		$.ajax({
            url    : '<?= Url::to(['invoice/get-payment-amount', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: 'json',
            success: function(response)
            {
                if (response.status) {
                    $('.payment-amount').val(response.amount);
                }
            }
        });
		return false;
  	});
	$(document).on('click', '.payment-cancel-btn', function (e) {
		$('#payment-modal').modal('hide');
		return false;
  	});
	$(document).on('beforeSubmit', '#invoice-note-form', function (e) {
		$.ajax({
			url    : '<?= Url::to(['note/create', 'instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_INVOICE]); ?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('.invoice-note-content').html(response.data);
					$('#invoice-note-modal').modal('hide');
				}else
				{
				 $('#invoice-note-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#payment-form', function (e) {
		e.preventDefault();
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('#payment-modal').modal('hide');
					$.pjax.reload({container: '#invoice-payment-listing', replace:false, timeout: 6000});
					payment.onEditableGridSuccess();
				}else
				{
				 $('#payment-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});
	$(document).on("click", "#line-item-grid tbody > tr", function() {
		var lineItemId = $(this).data('key');	
		$.ajax({
			url    : '<?= Url::to(['invoice-line-item/update']); ?>?id=' + lineItemId,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('#line-item-edit-content').html(response.data);
					$('#line-item-edit-modal').modal('show');
				}
			}
		});
		return false;
	});
	$(document).on("click", "#payment-grid tbody > tr", function() {
		var paymentId = $(this).data('key');	
		$.ajax({
			url    : '<?= Url::to(['payment/update']); ?>?id=' + paymentId,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('#payment-edit-content').html(response.data);
					$('#payment-edit-modal').modal('show');
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#line-item-edit-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
				{
					$.pjax.reload({container: '#line-item-listing', replace:false, timeout: 6000});
					payment.onEditableGridSuccess();
					invoice.onEditableGridSuccess();
					if(response.message) {
						$('#invoice-discount-warning').html(response.message).fadeIn().delay(8000).fadeOut();
					}
					$('#line-item-edit-modal').modal('hide');
				} else {
					$('#line-item-edit-form').yiiActiveForm('updateMessages', response.errors, true);
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#payment-edit-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container : '#invoice-payment-listing', timeout:6000});
					$('input[name="Payment[amount]"]').val(response.amount);
					payment.onEditableGridSuccess();
                    $('#payment-edit-modal').modal('hide');
				}else
				{
				 $(this).yiiActiveForm('updateMessages', response.errors, true);
				}
			}
			});
			return false;
	});
	$(document).on('click', '#payment-delete-button', function (e) {
		var paymentId = $('#payment-grid tbody > tr').data('key'); 
		$.ajax({
			url    : '<?= Url::to(['payment/delete']); ?>?id=' + paymentId,
			type   : 'get',
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container : '#invoice-payment-listing', timeout : 6000});
					payment.onEditableGridSuccess();
                                        $('#payment-edit-modal').modal('hide');
				} 
			}
			});
			return false;
	});
	$(document).on("click", '.line-item-cancel', function() {
		$('#line-item-edit-modal').modal('hide');
		return false;
	});
	$(document).on("click", '.payment-cancel', function() {
		$('#payment-edit-modal').modal('hide');
		return false;
	});
});
</script>