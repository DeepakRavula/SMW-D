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

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoice' : 'Invoice';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'InvoiceSearch[type]' => $model->type], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>
<div id="invoice-discount-warning" style="display:none;" class="alert-warning alert fade in"></div>
<style>
  .invoice-view .logo>img{
    width: 216px;
  }
    .badge{
      border-radius: 50px;
      font-size: 18px;
      font-weight: 400;
      padding: 5px 15px;
    }
    .smw-search{
      left: 170px;
    }
    .invoice-index{
        padding-top:5px;
        padding-right:15px;
        position: relative;
    }
    #invoice-mail-modal > .modal-dialog {
    	width: 900px !important;
    }
</style>
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
<div class="invoice-index p0">
		<?= Html::a('<i class="fa fa-envelope-o"></i> Mail this Invoice', '#', [
			'id' => 'invoice-mail-button',
			'class' => 'btn btn-default pull-right  m-l-10']) ?>
        <?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default pull-right m-l-10', 'target' => '_blank']) ?>
<?php $form = ActiveForm::begin([
                'id' => 'mail-flag',
            ]);?>
		<?php if ((int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE): ?>
                    <?php if ((bool) !$model->isDeleted()): ?>
			<?=
			Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->id],
				[
				'class' => 'btn btn-primary pull-right',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this invoice?',
                                    'method' => 'post',
                                ],
				'id' => 'delete-button',
			])
			?>
                    <?php endif; ?>
			<div class='mail-flag'>
			<?=
            $form->field($model, 'isSent')->widget(SwitchInput::classname(),
                [
                'name' => 'isSent',
                'pluginOptions' => [
                    'handleWidth' => 60, 
                    'onText' => 'Sent',
                    'offText' => 'Not Sent',
                ],
            ])->label(false);
            ?>
			</div>
		<?php elseif($model->canRevert()): ?>
                    <?=
			Html::a('<i class="fa fa-remove"></i> Revert this invoice', ['revert-invoice', 'id' => $model->id],
				[
				'class' => 'btn btn-primary pull-right',
                                'data' => [
                                    'confirm' => 'Are you sure you want to revert this invoice?',
                                ],
				'id' => 'revert-button',
			])
			?>
                <?php endif; ?>
    <?php ActiveForm::end(); ?>
</div>
<?php if(empty($model->lineItem) || $model->lineItem->isMisc()) : ?>
<div class="tabbable-panel">
     <div class="tabbable-line">
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
</div>
<?php endif; ?>
<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$invoiceContent = $this->render('_view-invoice', [
    'model' => $model,
    'customer' => $customer,
    'searchModel' => $searchModel,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
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
            'label' => 'Details',
            'content' => $invoiceContent,
            'options' => [
                    'id' => 'invoice',
                ],
        ],
		[
            'label' => 'Notes',
            'content' => $noteContent,
            'options' => [
                'id' => 'note',
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
            'label' => 'Logs',
            'content' => $logContent,
            'options' => [
                    'id' => 'log',
                ],
        ],
    ],
]); ?>
</div>
</div>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit Line Item</h4>',
    'id' => 'line-item-edit-modal',
]); ?>

<div id="line-item-edit-content"></div>
<?php Modal::end();?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit Discounts</h4>',
    'id' => 'invoice-discount-modal',
]); ?>

<div id="invoice-discount-content"></div>
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
        $(document).on("click", '.discount-cancel', function() {
		$('#invoice-discount-modal').modal('hide');
		return false;
	});
        $(document).on("click", '#invoice-discount', function() {
            $.ajax({
                url    : '<?= Url::to(['invoice/discount', 'id' => $model->id]); ?>',
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#invoice-discount-content').html(response.data);
                        $('#invoice-discount-modal').modal('show');
                        $('#warning-notification').html('You have entered a \n\
                        non-approved Arcadia discount. All non-approved discounts \n\
                        must be submitted in writing and approved by Head Office \n\
                        prior to entering a discount, otherwise you are in breach \n\
                        of your agreement.').fadeIn();
                    }
                }
            });
            return false;
        });
        $(document).on("beforeSubmit", '#invoice-discount-form', function() {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#invoice-discount-modal').modal('hide');
                        payment.onEditableGridSuccess();
                    }
                }
            });
            return false;
        });
});
</script>