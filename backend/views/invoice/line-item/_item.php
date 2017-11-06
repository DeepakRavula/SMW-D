<?php
use yii\helpers\Html;
use backend\models\search\InvoiceSearch;
use yii\helpers\Url;
use common\models\User;
use yii\widgets\ActiveForm;
use kartik\editable\Editable;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\bootstrap\Modal;
use common\models\InvoiceLineItem;
use yii\widgets\Pjax;

?>
<?php echo $this->render('/invoice/_line-item', [
        'invoiceModel' => $model,
    ]) ?>
<?php Pjax::Begin(['id' => 'invoice-view-tab-item', 'timeout' => 6000]); ?>
<?php $boxTools = $this->render('_button', [
	'model' => $model,
]);?>
<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'boxTools' => $boxTools,
		'title' => 'Items',
		'withBorder' => true,
	])
	?>

<div style="margin-bottom: 10px">
  <?php $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
    $lastRole = end($roles);
    if(!empty($model->lineItem) && ($lastRole->name === User::ROLE_ADMINISTRATOR ||
        $lastRole->name === User::ROLE_OWNER)) :?>
    <div class="pull-right m-r-20">
        <a id="show-column"><i class="fa fa-caret-left fa-2x"></i></a>
        <a id="hide-column" style="display:none"><i class="fa fa-caret-down fa-2x"></i></a>
    </div>
    <?php endif; ?>
	<?php echo $this->render('/invoice/_view-line-item', [
		'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
		'searchModel' => $searchModel,
	]) ?>	
</div>
<div class="clearfix"></div>

<?php LteBox::end() ?>
 <?php Pjax::end(); ?>
<script>
$(document).ready(function() {
    $('.add-new-misc').click(function(){
        $('input[type="text"]').val('');
        $('.tax-compute').hide();
        $('#invoicelineitem-tax_status').val('');
        $('.misc-tax-status').show();
        $('#invoice-line-item-modal').modal('show');
        return false;
    });
    $('.add-misc-cancel').click(function(){
    	$('#invoice-line-item-modal').modal('hide');
   		return false;
    });
    $(document).on("click", '.invoice-apply-discount-cancel', function() {
		$('#apply-discount-modal').modal('hide');
		return false;
	});

    $(document).on('click', '.apply-discount', function () {
        $('#apply-discount-modal').modal('show');
  		return false;
    });
    $(document).on('click', '.invoice-discount-cancel', function () {
        $('#apply-discount-modal').modal('hide');
  		return false;
    });
    
    $('input[name="Invoice[isSent]"]').on('switchChange.bootstrapSwitch', function(event, state) {
	$.ajax({
            url    : '<?= Url::to(['invoice/update-mail-status', 'id' => $model->id]) ?>',
            type   : 'POST',
            dataType: "json",
			data   : $('#mail-flag').serialize(),
			
            success: function(response)
            {
            }
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
                //invoice.updateSummarySectionAndStatus();
				$('#invoice-line-item-modal').modal('hide');
                //$.pjax.reload({container: "#invoice-lineitem-view", replace: false, async: false, timeout: 6000});
               // $.pjax.reload({container: "#invoice-view-lineitem-listing", replace: false, async: false, timeout: 6000});
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
$('#delete-button').click(function(){
    $.ajax({
        url    : '<?= Url::to(['invoice/delete', 'id' => $model->id]) ?>',
        type   : 'post',
        dataType: 'json',
        success: function(response)
        {
           if(response.status)
           {
                window.location.href = response.url;
                }else
                {
                    $('#invoice-error-notification').html(response.errors).fadeIn().delay(5000).fadeOut();
                }
        }
    });
    return false;
});
</script>    