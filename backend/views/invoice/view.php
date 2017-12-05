<?php

use yii\helpers\Html;
use backend\models\search\InvoiceSearch;
use yii\bootstrap\Tabs;
use common\models\InvoiceLineItem;
use common\models\Note;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use common\models\Payment;
use common\models\UserProfile;
use common\models\UserEmail;
use yii\data\ActiveDataProvider;
use backend\models\EmailForm;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->getInvoiceNumber();
$this->params['label'] = $this->render('_title', [
	'model' => $model,
]);
$this->params['action-button'] = $this->render('_buttons', [
	'model' => $model,
]); ?>
<?php if ((int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE): ?>
<?php $this->params['show-all'] = $this->render('_show-all', [
	'model' => $model,
]); ?>
<?php endif; ?>
<div id="customer-update" style="display:none;" class="alert-success alert fade in"></div>
<div id="invoice-discount-warning" style="display:none;" class="alert-warning alert fade in"></div>
<div id="invoice-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<?php Pjax::begin([
	'id' => 'invoice-view',
]);?>
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
		$this->render('customer/view', [
			'model' => $model,
			'customer' => $customer,
			'searchModel' => $searchModel,
		]);
		?>	
	</div>
	
	<?php endif; ?>
</div>

<?php
$lineItem = InvoiceLineItem::find()->notDeleted()->andWhere(['invoice_id' => $model->id])->one();
if (!empty($lineItem)) {
    $itemTypeId = $lineItem->item_type_id;
} else {
    $itemTypeId = null;
}

?>
<?php
$invoiceLineItemsDataProvider = new ActiveDataProvider([
    'query' => InvoiceLineItem::find()
        ->notDeleted()
        ->andWhere(['invoice_id' => $model->id]),
	'pagination' => false,
]);
$content = $this->render('mail/content', [
		'model' => $model,
		'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
	]);
?>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Email Preview</h4>',
    'id'=>'invoice-mail-modal'
]);
 echo $this->render('/mail/_form', [
	'model' => new EmailForm(),
	'emails' => !empty($model->user->email) ?$model->user->email : null,
	'subject' => 'Invoice from Arcadia Academy of Music',
	'content' => $content,
	'id' => $model->id,
        'userModel'=>$model->user,
]);
Modal::end();
?>

<?php Pjax::end(); ?>
<div class="row">
	<div class="col-md-12">  
		<?=
		$this->render('line-item/_item', [
			'model' => $model,
			'customer' => $customer,
			'searchModel' => $searchModel,
			'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                        'itemDataProvider' => $itemDataProvider
		]);
		?>   
	</div>
</div>
<div class="row">
	<div class="col-md-8">     
		<?= $this->render('payment/_index', [
			'model' => $model,
			'invoicePayments' => $invoicePayments,
			'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
		]);?>
	</div>
	<?php Pjax::Begin(['id' => 'invoice-bottom-summary', 'timeout' => 6000]); ?>
	<div class="col-md-4">
		<?=
		$this->render('_view-bottom-summary', [
			'model' => $model,
		]);
		?>	
	</div>
    <?php Pjax::end(); ?>
</div>
<div class="row">
	<div class="col-md-6">
		<?=
		 $this->render('note/view', [
			'model' => new Note(),
			'noteDataProvider' => $noteDataProvider
		]);
		?>
	</div>
	<?php Pjax::Begin(['id' => 'invoice-user-history', 'timeout' => 6000]); ?>
	<div class="col-md-6">
		<?=
		$this->render('log', [
			'model' => $model,
		]);
		?>	
	</div>
	<?php Pjax::end(); ?>
</div>
<div class="row">
	<div class="col-md-12">
		<?=
		$this->render('note/_reminder', [
			'model' => $model,
		]);
		?>
	</div>
</div>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit Line Item</h4>',
    'id' => 'line-item-edit-modal',
]); ?>

<div id="line-item-edit-content"></div>
<?php Modal::end();?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Message</h4>',
    'id' => 'message-modal',
]); ?>
<?= $this->render('note/_form', [
	'model' => $model,
]); ?>
<?php Modal::end();?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit Payment</h4>',
    'id' => 'payment-edit-modal',
]); ?>
<div id="payment-edit-content"></div>
<?php Modal::end();?>
<?php Modal::begin([
    'id' => 'invoice-customer-modal',
	'footer' => Html::a('Cancel', '#', ['class' => 'btn btn-default pull-right customer-cancel'])
]); ?>
<?php Modal::end();?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Add Walk-in</h4>',
    'id' => 'walkin-modal',
]); ?>
<?= $this->render('customer/_walkin', [
    'model' => $model,
    'userModel' => empty($model->user) ? new UserProfile() : $model->user->userProfile,
    'userEmail' => empty($model->user->primaryEmail->email) ? new UserEmail() : $model->user->primaryEmail,
]);?>
<?php Modal::end();?>
<?php Modal::begin([
     'header' => '<h4 class="m-0">Edit Tax</h4>',
     'id' => 'edit-tax-modal',
 ]); ?>
<div id="edit-tax-modal-content"></div>
 <?php Modal::end();?>
<script>
 $(document).ready(function() {
    $(document).on('click', '.edit-tax', function () {
        var selectedRows = $('#line-item-grid').yiiGridView('getSelectedRows');
        var params = $.param({ 'InvoiceLineItem[ids]' : selectedRows });
        if ($.isEmptyObject(selectedRows)) {
            $('#invoice-error-notification').html('Please select atleast a item to edit tax!').fadeIn().delay(5000).fadeOut();
        } else {
            $.ajax({
                url    : '<?= Url::to(['tax-status/edit-line-item-tax']) ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#edit-tax-modal .modal-dialog').css({'width': '400px'});
                        $('#edit-tax-modal').modal('show');
                        $('#edit-tax-modal-content').html(response.data);
                    }
                }
            });
        }
    });

    $(document).on('click', '.edit-item', function () {
        var selectedRows = $('#line-item-grid').yiiGridView('getSelectedRows');
        var params = $.param({ id : selectedRows[0] });
        if ($.isEmptyObject(selectedRows)) {
            $('#invoice-error-notification').html('Please select atleast a item to edit!').fadeIn().delay(5000).fadeOut();
        } else {
            $.ajax({
                url    : '<?= Url::to(['invoice-line-item/update']) ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#line-item-edit-modal .modal-dialog').css({'width': '600px'});
                        $('#line-item-edit-modal').modal('show');
                        $('#line-item-edit-content').html(response.data);
                    }
                }
            });
        }
    });
 	$(document).on('click', '.edit-tax-cancel', function (e) {
 		$('#edit-tax-modal').modal('hide');
 		return false;
   	}); 
	$(document).on('click', '.add-invoice-note', function (e) {
		$('#message-modal').modal('show');
		return false;
  	});
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
	$(document).on('click', '.add-customer', function (e) {
		$.ajax({
			url    : $(this).attr('href'),
			type: 'get',
			dataType: "json",
			success: function (response)
			{
				if (response.status)
				{
					$('#invoice-customer-modal .modal-body').html(response.data);
					$('#invoice-username').val('');
					$('#invoice-customer-modal').modal('show');
					$('#invoice-customer-modal .modal-dialog').css({'width': '800px'});
				} 
			}
		});
		return false;
  	});
	$(document).on('click', '.customer-cancel', function (e) {
		$('#invoice-customer-modal').modal('hide');
           var url = "<?php echo Url::to(['invoice/view']); ?>?id=" + <?php echo $model->id; ?>;
            window.location=url;
		return false;
  	});
    $(document).on('click', '.add-walkin', function (e) {
		$('#walkin-modal .modal-dialog').css({'width': '400px'});
		$('#walkin-modal').modal('show');
		return false;
  	});
        
    $(document).on('click', '.invoice-customer-update-cancel-button', function (e) {
		$('#invoice-customer-modal').modal('hide');
		return false;
  	});
        $(document).on('click', '.walkin-cancel-button', function (e) {
		$('#walkin-modal').modal('hide');
		return false;
  	});
	$(document).on('click', '.invoice-note-cancel', function (e) {
		$('#message-modal').modal('hide');
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
	$(document).on('click', '.apply-credit-cancel', function (e) {
		$('#credit-modal').modal('hide');
		return false;
  	});
	$(document).on('click', '.add-payment', function (e) {
        $('#payment-modal .modal-dialog').css({'width': '400px'});
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
	$(document).on('beforeSubmit', '#mail-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   	{
                    $('#spinner').hide();		
                    $('#invoice-mail-modal').modal('hide');
					$('#success-notification').html(response.message).fadeIn().delay(5000).fadeOut();
					$('.mail-flag').html(response.data);
					
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
		$(document).on('beforeSubmit', '#invoice-message-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container: '#invoice-view', replace:false, timeout: 6000});
					$('#message-modal').modal('hide');
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
					$.pjax.reload({container: "#invoice-view-payment-tab", replace:false,async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
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
        $('#item-edit-spinner').show();
            $.ajax({
                    url    : $(this).attr('action'),
                    type   : 'post',
                    dataType: "json",
                    data   : $(this).serialize(),
                    success: function(response)
                    {
                       if(response.status)
                            {
                                    $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
               $.pjax.reload({container: "#invoice-view-lineitem-listing", replace: false, async: false, timeout: 6000}); 
                                    if(response.message) {
                                            $('#success-notification').html(response.message).fadeIn().delay(8000).fadeOut();
                                    }
                                    $('#line-item-edit-modal').modal('hide');
                                    $('#item-edit-spinner').hide();
                            } else {
                                    $('#line-item-edit-form').yiiActiveForm('updateMessages', response.errors, true);
                                    $('#item-edit-spinner').hide();
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
					$.pjax.reload({container: "#invoice-view-payment-tab", replace:false,async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
					$('input[name="Payment[amount]"]').val(response.amount);
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
					$.pjax.reload({container : '#invoice-view', timeout : 6000});
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
    $(document).on("click", '.mail-view-cancel-button', function() {
		$('#invoice-mail-modal').modal('hide');
		return false;
	});
	$(document).on("click", '.payment-cancel', function() {
		$('#payment-edit-modal').modal('hide');
		return false;
	});
	$(document).on('beforeSubmit', '#customer-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container : '#invoice-view', async : false, timeout : 6000});
					$('#customer-update').html(response.message).fadeIn().delay(8000).fadeOut();
                                        $('#invoice-customer-modal').modal('hide');
				}else
				{
				 $('#customer-form').yiiActiveForm('updateMessages',
					response.errors, true);
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#walkin-customer-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container : '#invoice-view', async : false, timeout : 6000});
					$('#customer-update').html(response.message).fadeIn().delay(8000).fadeOut();
                    $('#walkin-modal').modal('hide');
				}else
				{
				 $('#walkin-customer-form').yiiActiveForm('updateMessages',
					response.errors, true);
				}
			}
		});
		return false;
	});
        $(document).on("click", '.add-customer-invoice', function() {
             var customerId=$(this).attr('data-key');
             var params = $.param({'customerId': customerId });
             $.ajax({
            url    : '<?= Url::to(['invoice/update-customer', 'id' => $model->id,]); ?>&' + params,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
                    var url = "<?php echo Url::to(['invoice/view']); ?>?id=" + <?php echo $model->id; ?>;
                    $.pjax.reload({url:url,container : '#invoice-view', async : false, timeout : 6000});
					$('#customer-update').html(response.message).fadeIn().delay(8000).fadeOut();
                    $('#invoice-customer-modal').modal('hide');
                               
				}else
				{
				 
				}
			}
		});
		return false;
	});
});
</script>