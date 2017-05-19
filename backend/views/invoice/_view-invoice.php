<?php
use yii\helpers\Html;
use backend\models\search\InvoiceSearch;
use yii\helpers\Url;
use common\models\User;
use yii\widgets\ActiveForm;
use kartik\editable\Editable;

?>
<style>
.table-invoice-childtable>tbody>tr>td:last-of-type {
    text-align: right;
}
</style>
<div id="invoice-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="invoice-view p-10">
		    <div class="row">
            <a href="<?= Yii::getAlias('@frontendUrl') ?>" class="logo invoice-col col-sm-3">              
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
          <div class="col-sm-4 invoice-address invoice-col text-gray">
              <div class="row-fluid">
                <h2 class="m-0 text-inverse"><strong>
                  <?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'INVOICE'?> </strong>
                </h2>
              </div>
              <small>
                <?php if (!empty($model->user->userLocation->location->address)): ?>
                  <?= $model->user->userLocation->location->address?><br>
          			<?php endif; ?>
          			<?php if (!empty($model->user->userLocation->location->phone_number)): ?>
                  <?= $model->user->userLocation->location->phone_number?>
          			<?php endif; ?> 
                                <?php if (!empty($model->user->userLocation->location->email)): ?>
                  <?= $model->user->userLocation->location->email?>
          			<?php endif; ?> 
              </small> 
            </div>
            <?php if (!empty($customer)):?>
            <div class="col-sm-2 invoice-col">
              To
              <address>
                <strong>
                  <a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => 'customer', 'id' => $customer->id]) ?>">
                        <?= isset($customer->publicIdentity) ? $customer->publicIdentity : null?>
                  </a></strong>
                  <br>
                  <?php
                      $addresses = $customer->addresses;
                      foreach ($addresses as $address) {
                          if ($address->label === 'Billing') {
                              $billingAddress = $address;
                              break;
                          }
                      }
                      $phoneNumber = $customer->phoneNumber;
                  ?>
                <!-- Billing address -->
                <?php if (!empty($billingAddress)) {
                    echo $billingAddress->address.'<br> '.$billingAddress->city->name.', ';
                    echo $billingAddress->province->name.'<br>'.$billingAddress->country->name.' ';
                    echo $billingAddress->postal_code;
                } ?>
            
               <?php if (!empty($customer->email)): ?>
               <?= 'E: '; ?><?= $customer->email?><br>
               <?php endif; ?>
            
            <!-- Phone number -->
            
              <?php if (!empty($phoneNumber)) : ?>
                <div class="row-fluid"><?= 'P: '; ?><?= $phoneNumber->number; ?>
                </div>
              <?php endif; ?>
              </address>
            </div>
             <?php endif; ?>
            <div class="col-sm-2 invoice-col">
              <b>Invoice <?= '#'.$model->getInvoiceNumber()?></b><br><br>
              <b>Date:</b> <?= Yii::$app->formatter->asDate($model->date); ?><br>
              <?php if (!$model->isInvoice()) : ?>
              <b>Due Date:</b> <?= Yii::$app->formatter->asDate($model->dueDate); ?><br>
              <?php endif; ?>
               <b>Status:</b> <div id="invoice-status"><?= $model->getStatus(); ?></div>
            </div>
          <div class="clearfix"></div>
        </div>
        <!-- /.col -->
    <div class="invoice-info m-t-20">
        <!-- /.col -->


		
	<?php if((empty($model->lineItem) || $model->lineItem->isOtherLineItems()) && $model->isInvoice()) :?>
	<div id="add-misc-item" class="col-sm-2">
    <div class="m-b-20">
	<a href="#" class="add-new-misc text-add-new"><i class="fa fa-plus-circle"></i> Add Misc</a>
	<div class="clearfix"></div>
    </div>
	</div>
	<div id="add-book" class="col-sm-2">
		<div class="m-b-20">
		<a href="#" class="text-add-new"><i class="fa fa-plus-circle"></i> Add Book</a>
		<div class="clearfix"></div>
		</div>
 	</div>
    <?php endif; ?>
    <?php if(!empty($model->lineItem) && (!$model->lineItem->isOpeningBalance())) :?>
    <div id="apply-discount" class="col-sm-2">
    <div class="m-b-20">
	<a href="#" class="add-new-misc text-add-new"><i class="fa fa-plus-circle"></i> Apply Discount</a>
	<div class="clearfix"></div>
    </div>
    </div>
    <?php endif; ?>
    <?php $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
    $lastRole = end($roles);
    if(!empty($model->lineItem) && ($lastRole->name === User::ROLE_ADMINISTRATOR ||
        $lastRole->name === User::ROLE_OWNER)) :?>
    <div class="pull-right m-r-20">
        <a id="show-column"><i class="fa fa-caret-left fa-2x"></i></a>
        <a id="hide-column" style="display:none"><i class="fa fa-caret-down fa-2x"></i></a>
    </div>
    <?php endif; ?>
	<?php echo $this->render('_line-item', [
        'invoiceModel' => $model,
    ]) ?>
	<?php echo $this->render('_view-line-item', [
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        'searchModel' => $searchModel,
    ]) ?>
    <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
          <div class="table-responsive">
            <table class="table table-invoice-total">
              <tbody>
                <tr>
                  <td colspan="4">
                    
                    <div class="row-fluid m-t-20">
					<em><strong>Notes:</strong></em><Br>
					<?=
					 Editable::widget([
						'name'=>'notes', 
						'asPopover' => true,
						'inputType' => Editable::INPUT_TEXTAREA,
						'value' => $model->notes,
						'header' => 'Printed Notes',
						'submitOnEnter' => false,
						'size'=>'lg',
						'options' => ['class'=>'form-control', 'rows'=>5, 'placeholder'=>'Enter Printed notes...'],
					])
					?>  
                    </div>
                    
                  </td>
                  <td colspan="2">
                    <table id="invoice-summary-section" class="table-invoice-childtable">
					<?php
                        echo $this->render('_view-bottom-summary', [
                            'model' => $model,
                    ]); ?>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- /.col -->
        </div>
<div class="clearfix"></div>
<div class="no-print">
  <div class="col-xs-12">
  <!-- <hr class="default-hr">   -->
  </div>
</div>
</div>
<div class="reminder_notes text-muted well well-sm no-shadow">
    <?php echo $model->reminderNotes; ?>
</div>
</div>
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
    },
}
$(document).ready(function() {
    $('#add-misc-item').click(function(){
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
	$('#add-book').click(function(){
		$('.tax-compute').show();
		$('.misc-tax-status').hide();
		$('input[type="text"]').val('');
		$('#invoicelineitem-tax_type').val('GST');
		$('#invoicelineitem-tax_code').val('ON');
		$('#invoicelineitem-tax').val('5.00');
		$('#invoicelineitem-tax_status').val('3');
		$('#invoice-line-item-modal').modal('show');
       	return false;
    });
    $('#apply-discount').click(function(){
        $('#apply-discount-modal').modal('show');
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
    
    $('#show-column').click(function(){
        var url = "<?php echo Url::to(['invoice/view', 'id' => $model->id]); ?>&InvoiceSearch[toggleAdditionalColumns]="  + 1;
        $.pjax.reload({url:url,container:"#line-item-listing",replace:false,  timeout: 4000});  //Reload GridView
        $('#show-column').hide();
        $('#hide-column').toggle();
    });

    $('#hide-column').click(function(){
        var url = "<?php echo Url::to(['invoice/view', 'id' => $model->id]); ?>&InvoiceSearch[toggleAdditionalColumns]="  + 0;
        $.pjax.reload({url:url,container:"#line-item-listing",replace:false,  timeout: 4000});  //Reload GridView
        $('#hide-column').hide();
        $('#show-column').toggle();
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
