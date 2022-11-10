<?php
use yii\helpers\Url;
use common\models\User;
use common\models\Invoice;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<?php echo $this->render('/invoice/_line-item', [
        'invoiceModel' => $model,
        'itemDataProvider' => $itemDataProvider,
        'searchModel' => $searchModel,
        'itemSearchModel' => $itemSearchModel,
    ]) ?>
<?php Pjax::Begin(['id' => 'invoice-view-tab-item', 'timeout' => 6000]); ?>
<?php $boxTools = $this->render('_button', [
    'model' => $model,
]);?>
<?php
	if($model->type == Invoice::TYPE_PRO_FORMA_INVOICE) {
        $panelName = 'Lessons';
        $boxTools = '';
	} else {
		$panelName = 'Items';
	}
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => $boxTools,
        'title' => $panelName,
        'withBorder' => true,
    ])
    ?>

<div style="margin-bottom: 10px">
  <?php $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
    $lastRole = end($roles);
    if (!empty($model->lineItem) && ($lastRole->name === User::ROLE_ADMINISTRATOR ||
        $lastRole->name === User::ROLE_OWNER)) :?>
    <div class="pull-right m-r-20">
        <a id="show-column"><i class="fa fa-caret-left fa-2x"></i></a>
        <a id="hide-column" style="display:none"><i class="fa fa-caret-down fa-2x"></i></a>
    </div>
    <?php endif; ?>
	<?php echo $this->render('/invoice/_view-line-item', [
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        'searchModel' => $searchModel,
        'model' => $model, 
    ]) ?>	
</div>
<div class="clearfix"></div>

<?php LteBox::end() ?>
 <?php Pjax::end(); ?>
<script>
$(document).ready(function() {
    $(document).on('click', '.add-new-misc', function () {
        $.ajax({
            url    : '<?= Url::to(['invoice/show-items', 'id' => $model->id]); ?>',
            type   : 'get',
            success: function(response)
            {
                if (response.status) {
                    $('#popup-modal').modal('show');
                    $('#popup-modal .modal-dialog').css({'width': '600px'});
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Add Line Items</h4>');
                    $('#modal-content').html(response.data);
                }
            }
        });
        return false;
    });
    $(document).on('click', '.add-misc-cancel', function () {
    	$('#invoice-line-item-modal').modal('hide');
        $.pjax.reload({container: "#invoice-view-tab-item", replace: false, async: false, timeout: 6000});
        return false;
    });
    $(document).on("click", '.invoice-apply-discount-cancel', function() {
		$('#apply-discount-modal').modal('hide');
		return false;
	});

    $(document).on('click', '.apply-discount', function () {
        var selectedRows = $('#line-item-grid').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(selectedRows)) {
            $('#invoice-error-notification').html('Please select atleast a item to edit discount!').fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ 'InvoiceLineItem[ids]': selectedRows });
            $.ajax({
                url    : '<?= Url::to(['invoice-line-item/apply-discount']) ?>?' + params,
                type   : 'get',
                dataType: "json",
                success: function(response)
                {
                    if (response.status) {
                        $('#apply-discount-modal').modal('show');
                        $('#apply-discount-content').html(response.data);
                    } else {
                        $('#invoice-error-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        }
    });
    $(document).on('click', '.invoice-discount-cancel', function () {
        $('#apply-discount-modal').modal('hide');
  		return false;
    });
    
    $('input[name="Invoice[isSent]"]').on('switchChange.bootstrapSwitch', function(event, state) {
		var params = $.param({'state' : state | 0});
	$.ajax({
            url    : '<?= Url::to(['invoice/update-mail-status', 'id' => $model->id]) ?>&' + params,
            type   : 'POST',
            dataType: "json",
            data   : $('#mail-flag').serialize()
        });
        return false;
    });
    
    $(document).on('click', '#show-column' ,function(){
        var url = "<?php echo Url::to(['invoice/view', 'id' => $model->id]); ?>&InvoiceSearch[toggleAdditionalColumns]="  + 1;
        $.pjax.reload({url:url,container:"#invoice-view-lineitem-listing",replace:false,  timeout: 4000});  //Reload GridView
        $('#show-column').hide();
        $('#hide-column').toggle();
    });

    $(document).on('click', '#hide-column' ,function(){
        var url = "<?php echo Url::to(['invoice/view', 'id' => $model->id]); ?>&InvoiceSearch[toggleAdditionalColumns]="  + 0;
        $.pjax.reload({url:url,container:"#invoice-view-lineitem-listing",replace:false,  timeout: 4000});  //Reload GridView
        $('#hide-column').hide();
        $('#show-column').toggle();
    }); 
    $(document).on('beforeSubmit', '#add-misc-item-form', function (e) {
	$.ajax({
		url    : $(this).attr('action'),
		type   : 'post',
		dataType: "json",
		data   : $(this).serialize(),
		success: function(response)
		{
		   if(response.status)
		   {			
				$('input[name="Payment[amount]"]').val(response.amount);
				$('#invoice-line-item-modal').modal('hide');
                $.pjax.reload({container: "#invoice-header-summary", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-view-tab-item", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
                
			}else
			{
			 $(this).yiiActiveForm('updateMessages', response.errors, true);
			}
		}
		});
		return false;
});
});
</script>
<script>
    $(document).on('click', '#invoice-delete-button', function () {
        bootbox.confirm({
            message: "Are you sure you want to delete this invoice?",
                callback: function(result){
                    if(result) {
                        $('.bootbox').modal('hide');
                        $.ajax({
                            url: '<?= Url::to(['invoice/delete', 'id' => $model->id]) ?>',
                            type: 'post',
                            success: function (response)
                            {
                                if (response.status) {
                                    window.location.href = response.url;
                                } else {
                                    $('#invoice-error-notification').html(response.errors).fadeIn().delay(5000).fadeOut();
                                }
                            }
                       });
                       return false;
                    }
                }
        });
        return false;
    });
</script>    