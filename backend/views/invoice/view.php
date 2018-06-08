<?php

use backend\models\search\InvoiceSearch;
use common\models\InvoiceLineItem;
use common\models\Note;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use common\models\UserProfile;
use common\models\UserEmail;
use yii\imperavi\TableImperaviRedactorPluginAsset;
TableImperaviRedactorPluginAsset::register($this);
use kartik\select2\Select2Asset;
Select2Asset::register($this);
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
<div id="invoice-spinner" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>
<div id="line-item-update" style="display:none;" class="alert-success alert fade in"></div>
<div id="customer-update" style="display:none;" class="alert-success alert fade in"></div>
<div id="invoice-discount-warning" style="display:none;" class="alert-warning alert fade in"></div>
<div id="invoice-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<br>
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
    $amount = 0.00;
    if ($model->total > $model->invoicePaymentTotal) {
        $amount = $model->balance;
    }
    if (empty($amount)) {
        $amount = 0.00;
    }
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
            'itemDataProvider' => $itemDataProvider,
            'itemSearchModel'=>$itemSearchModel,
        ]);
        ?>   
	</div>
</div>
<div class="row">
	<div class="col-md-9">     
        
	</div>
	<?php Pjax::Begin(['id' => 'invoice-bottom-summary', 'timeout' => 6000]); ?>
	<div class="col-md-3">
		<?=
        $this->render('_view-bottom-summary', [
            'model' => $model,
        ]);
        ?>	
	</div>
    <?php Pjax::end(); ?>
</div>
<div class="row">
<?php Pjax::Begin(['id' => 'invoice-message-panel', 'timeout' => 6000]); ?>
   <div class="col-md-3">
		<?=
         $this->render('_message', [
            'model' => $model,
        ]);
        ?>
	</div>
    <?php Pjax::end(); ?>
	<div class="col-md-4">
		<?=
         $this->render('note/view', [
            'model' => new Note(),
            'noteDataProvider' => $noteDataProvider
        ]);
        ?>
	</div>
	<?php Pjax::Begin(['id' => 'invoice-user-history', 'timeout' => 6000]); ?>
	<div class="col-md-5">
		<?=
        $this->render('log', [
            'model' => $model,
            'logDataProvider' =>$logDataProvider,
        ]);
        ?>	
	</div>
	<?php Pjax::end(); ?>
</div>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Message</h4>',
    'id' => 'message-modal',
]); ?>
<?= $this->render('note/_form', [
    'model' => $model,
]); ?>
<?php Modal::end();?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Details</h4>',
    'id' => 'invoice-detail-modal',
]); ?>
<?= $this->render('_detail-form', [
    'model' => $model,
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
    'footer' => $this->render('_submit-button', [
        'deletable' => false,
        'saveClass' => 'edit-tax-save',
        'cancelClass' => 'edit-tax-cancel'
    ])
 ]); ?>
<div id="edit-tax-modal-content"></div>
 <?php Modal::end();?>
<?php Modal::begin([
     'header' => '<h4 class="m-0">Adjust Tax</h4>',
     'id' => 'adjust-tax-modal',
    'footer' => $this->render('_submit-button', [
        'deletable' => false,
        'saveClass' => 'adjust-tax-form-save',
        'cancelClass' => 'tax-adj-cancel'
    ])
 ]); ?>
<div id="adjust-tax-modal-content"></div>
<?php Modal::end();
Modal::begin([
    'header' =>  '<h4 class="m-0 pull-left">Choose Customer</h4>',
    'id' => 'customer-modal',
    'closeButton' => false,
]); ?>
<?= $this->render('customer/_list', [
        'model' => $model,
        'searchModel' => $userSearchModel,
        'userDataProvider' => $userDataProvider
]); ?>
<?php Modal::end(); ?>

<script>
 $(document).ready(function() {
    $(document).on('click', '.edit-tax', function () {
        var selectedRows = $('#line-item-grid').yiiGridView('getSelectedRows');
        var params = $.param({ 'InvoiceLineItem[ids]' : selectedRows });
        if ($.isEmptyObject(selectedRows)) {
            $('#invoice-error-notification').html('Please select atleast a item to edit tax!').fadeIn().delay(5000).fadeOut();
        } else {
            $.ajax({
                url    : '<?= Url::to(['invoice-line-item/edit-tax']) ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#edit-tax-modal .modal-dialog').css({'width': '400px'});
                        $('#edit-tax-modal').modal('show');
                        $('#edit-tax-modal-content').html(response.data);
                    } else {
                        $('#invoice-error-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        }
    });

    $(document).off('click', '#line-item-grid table tr').on('click', '#line-item-grid table tr', function () {
        var id = $(this).data('key');
        var selectedRows = $('#line-item-grid').yiiGridView('getSelectedRows');
        if (!$.isEmptyObject(selectedRows)) {
            $('#invoice-error-notification').html('You are not allowed to perform this action!').fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ id : id });
            var url = '<?= Url::to(['invoice-line-item/delete']) ?>?' + params;
            $.ajax({
                url    : '<?= Url::to(['invoice-line-item/update']) ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#popup-modal .modal-dialog').css({'width': '600px'});
                        $('#popup-modal').modal('show');
                        $('.modal-delete').show();
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Line Item</h4>');
                        $('.modal-delete').attr('action', url);
                        $('#modal-content').html(response.data);
                        $('.modal-delete').attr('message', response.deleteConfirmation);
                    } else {
                        $('#invoice-error-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        }
        return false;
    });

    $(document).on('modal-error', function (event, params) {
        if (params.message) {
            $('#modal-popup-error-notification').html(params.message).fadeIn().delay(5000).fadeOut();
        }
    });

    $(document).on('modal-success', function (event, params) {
        if (!$.isEmptyObject(params.message)) {
            $('#success-notification').html(params.message).fadeIn().delay(5000).fadeOut();
        }
        if (!$.isEmptyObject(params.data)) {
            $('.mail-flag').html(params.data);
        }
        invoice.reload();
    });
    
    $(document).on('modal-delete', function (event, params) {
        invoice.reload();
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
		$('#customer-modal').modal('show');
		return false;
  	});
    $(document).on('click', '.invoice-detail', function (e) {
		$('#invoice-detail-modal').modal('show');
        $('#invoice-detail-modal .modal-dialog').css({'width': '400px'});
		return false;
  	});
    $(document).on('click', '.invoice-detail-cancel', function (e) {
		$('#invoice-detail-modal').modal('hide');
		return false;
  	})
	$(document).on('click', '.add-customer-cancel', function (e) {
		$('#customer-modal').modal('hide');
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
            $.ajax({
                url    : '<?= Url::to(['email/invoice', 'id' => $model->id]); ?>',
                type   : 'get',
                dataType: 'json',
                success: function(response)
                {
                    if (response.status) {
                        $('#modal-content').html(response.data);
                        $('#popup-modal').modal('show');
                        $('#popup-modal .modal-dialog').css({'width': '1000px'});
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Email Preview</h4>');
                        $('.modal-save').text('Send');
                    }
                }
            });
            return false;
  	});
	$(document).on('click', '.apply-credit', function (e) {
            $.ajax({
                url    : '<?= Url::to(['payment/credit-payment', 'id' => $model->id]); ?>',
                type   : 'get',
                dataType: 'json',
                success: function(response)
                {
                    if (response.status && response.hasCredit) {
                        var amountNeeded = <?= $amount; ?>;
                        $('#modal-content').html(response.data);
                        $('#popup-modal').modal('show');
                        $('#payment-amountneeded').val((amountNeeded).toFixed(2));
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Apply Credit</h4>');
                        $('.modal-save').text('Pay now')
                    } else {
bootbox.alert({
title: 'Apply Credit',
message: "<div class='text-center'><span class='fa fa-warning apply-credit-error-alert'>  "+
       response.message +
   "   </span></div>",
class: "small",
});
                    }
                }
            });
            return false;
  	});

        $(document).on('click', '.add-payment', function (e) {
        $('#payment-modal .modal-dialog').css({'width': '400px'});
		$('#payment-modal').modal('show');
                $('.create-payment').attr('disabled', false);
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
					$.pjax.reload({container: '#invoice-message-panel', replace:false, timeout: 6000});
					$('#message-modal').modal('hide');
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#payment-form', function (e) {
            $('.create-payment').attr('disabled', true);
            $('#add-payment-spinner').show();
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
                                $('#payment-add-spinner').hide();
                                $('#payment-modal').modal('hide');
                                if (response.canAlert) {
                                    invoice.postAfterPaid();
                                }
                                invoice.reload();
                                $('#add-payment-spinner').hide();
                            } else {
                                $('#payment-form').yiiActiveForm('updateMessages', response.errors , true);
                                $('.create-payment').attr('disabled', false);
                                $('#add-payment-spinner').hide();
                            }
			}
		});
		return false;
	});

    $(document).on("click", "#payment-grid tbody > tr", function() {
        var paymentId = $(this).data('key');
        var url = '<?= Url::to(['payment/delete']); ?>?id=' + paymentId;
        $.ajax({
            url    : '<?= Url::to(['payment/update']); ?>?id=' + paymentId,
            type   : 'get',
            dataType: "json",
            success: function(response)
            {
                if(response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Payment</h4>');
                    $('#popup-modal .modal-dialog').css({'width': '400px'});
                    $('.modal-delete').show();
                    $('.modal-delete').attr('action', url);
                }
            }
        });
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
       $('#customer-spinner').show();
         var customerId=$(this).attr('data-key');
         var params = $.param({'customerId': customerId });
    $.ajax({
        url    : '<?= Url::to(['invoice/update-customer' ,'id' => $model->id]); ?>&' + params,
                    type   : 'post',
                    dataType: "json",
                    data   : $(this).serialize(),
                    success: function(response)
                    {
                       if(response.status)
                       {
               $('#customer-spinner').hide();
                $.pjax.reload({container : '#invoice-view', async : false, timeout : 6000});
                                    $('#customer-update').html(response.message).fadeIn().delay(8000).fadeOut();
                $('#customer-modal').modal('hide');

                            }
                    }
            });
            return false;
    });
 
    $(document).on("click", '#print-btn', function() {
        var url = '<?= Url::to(['print/invoice' ,'id' => $model->id]); ?>';
        window.open(url,'_blank');
        return false;
    });
});

$(document).on("click", '.adjust-invoice-tax', function() {
    $('#customer-spinner').show();
    $.ajax({
        url: '<?= Url::to(['invoice/adjust-tax' ,'id' => $model->id]); ?>',
        type   : 'get',
        success: function(response)
        {
            if(response.status)
            {
                $('#customer-spinner').hide();
                $('#adjust-tax-modal').modal('show');
                $('#adjust-tax-modal-content').html(response.data);
            } else {
                $('#invoice-error-notification').html(response.message).fadeIn().delay(5000).fadeOut();
            }
        }
    });
});
$(document).on('beforeSubmit', '#invoice-detail-form', function (e) {
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
            $('#invoice-detail-modal').modal('hide');
            $('#success-notification').html('Invoice has been updated successfully').fadeIn().delay(5000).fadeOut();
        } else
        {
            $('#invoice-detail-form').yiiActiveForm('updateMessages',response.errors, true);
        }
    }
    });
       return false;
    });
    $(document).off('click', '.create-payment').on('click', '.create-payment', function () {
        $('.create-payment').attr('disabled', true);
        $('#payment-form').submit();
        return false;
    });

    $(document).on('afterValidate', '#payment-form', function (event, messages, errorAttributes) {
        if (errorAttributes.length > 0) {
            $('.create-payment').attr('disabled', false);
        }
    });

    $(document).off('click', '#post-distriute').on('click', '#post-distriute', function () {
        invoice.postAndDistribute();
        return false;
    });

    $(document).off('click', '#distriute').on('click', '#distriute', function () {
        invoice.distribute();
        return false;
    });

    $(document).off('click', '#retract').on('click', '#retract', function () {
        invoice.retract();
        return false;
    });

    $(document).off('click', '#un-post').on('click', '#un-post', function () {
        invoice.unpost();
        return false;
    });

    $(document).off('click', '#post').on('click', '#post', function () {
        invoice.post();
        return false;
    });

    $(document).off('click', '#void').on('click', '#void', function () {
        invoice.void();
        return false;
    });

    var invoice = {
        post: function() {
            $('#invoice-spinner').show();
            $.ajax({
                url    : '<?= Url::to(['invoice/post', 'id' => $model->id]); ?>',
                type   : 'post',
                dataType: "json",
                success: function(response)
                {
                    if(response.status)
                    {
                        invoice.reload();
                        $('#success-notification').html('PFI posted succesfully!').fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        },

        void: function() {
            bootbox.prompt({
                title: "Do you want to unschedule lesson",
                inputType: 'checkbox',
                inputOptions: [
                    {
                        text: 'Unschedule Lesson',
                        value: 'unschedule',
                    },
                ],
                callback: function (result) {
                    if (result) {
                        var isChecked = $('.bootbox-input-checkbox').is(':checked');
                        var params = $.param({'canbeUnscheduled': isChecked | 0 });
                        $('#invoice-spinner').show();
                        $.ajax({
                            url    : '<?= Url::to(['invoice/void', 'id' => $model->id]); ?>&'+params,
                            type   : 'post',
                            dataType: "json",
                            success: function(response)
                            {
                                if(response.status)
                                {
                                    invoice.reload();
                                    $('#success-notification').html('Invoice voided succesfully!').fadeIn().delay(5000).fadeOut();
                                }
                            }
                        });
                    }
                }
            });
        },
        
        postAfterPaid: function () {
            $('#invoice-spinner').show();
            bootbox.confirm({
                message: 'This PFI is now fully paid. Would you like to post this document and distribute the                                               payments received to the associated lessons?',
                callback: function(result) {
                    if (result) {
                        invoice.postAndDistribute();
                        $('#success-notification').html('PFI has been posted succesfully!').fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        },

        distribute: function() {
            $('#invoice-spinner').show();
            $.ajax({
                url    : '<?= Url::to(['invoice/distribute', 'id' => $model->id]); ?>',
                type   : 'post',
                dataType: "json",
                success: function(response)
                {
                    if(response.status)
                    {
                        invoice.reload();
                        $('#success-notification').html('Funds distributed succesfully!').fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        },

        postAndDistribute: function() {
            $('#invoice-spinner').show();
            $.ajax({
                url    : '<?= Url::to(['invoice/post-distribute', 'id' => $model->id]); ?>',
                type   : 'post',
                dataType: "json",
                success: function(response)
                {
                    if(response.status)
                    {
                        invoice.reload();
                        $('#success-notification').html('Funds distributed succesfully!').fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        },

        unpost: function() {
            $('#invoice-spinner').show();
            $.ajax({
                url    : '<?= Url::to(['invoice/unpost', 'id' => $model->id]); ?>',
                type   : 'post',
                dataType: "json",
                success: function(response)
                {
                    if(response.status)
                    {
                        invoice.reload();
                        $('#success-notification').html('PFI has been un-posted succesfully!').fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        },

        retract: function() {
            $('#invoice-spinner').show();
            $.ajax({
                url    : '<?= Url::to(['invoice/retract-credits', 'id' => $model->id]); ?>',
                type   : 'post',
                dataType: "json",
                success: function(response)
                {
                    if(response.status)
                    {
                        invoice.reload();
                        $('#success-notification').html('Funds retracted succesfully!').fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        },

        reload: function() {
            $.pjax.reload({container: "#invoice-details", replace: false, async: false, timeout: 6000});
            $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
            $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
            $.pjax.reload({container: "#invoice-header-summary", replace: false, async: false, timeout: 6000});
            $.pjax.reload({container: "#invoice-view-lineitem-listing", replace: false, async: false, timeout: 6000});
            $.pjax.reload({container: "#invoice-header-summary", replace: false, async: false, timeout: 6000});
            $.pjax.reload({container: "#invoice-view-payment-tab", replace:false,async: false, timeout: 6000});
            $.pjax.reload({container: "#invoice-title", replace:false,async: false, timeout: 6000});
            $.pjax.reload({container: "#invoice-view-tab-item", replace:false,async: false, timeout: 6000});
            $('#invoice-spinner').hide();
        }
    }
</script>
