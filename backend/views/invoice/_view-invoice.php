<?php
use yii\helpers\Html;
use backend\models\search\InvoiceSearch;
use yii\helpers\Url;
use common\models\Invoice;
use yii\widgets\ActiveForm;
use kartik\editable\Editable;

?>
<div id="invoice-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="invoice-view p-50">
         <div class="row">
		<div class="col-xs-12 p-0">
          <h2 class="m-0">
            <a href="<?= Yii::getAlias('@frontendUrl') ?>" class="logo pull-left">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
          <div class="pull-left invoice-address text-gray">
            <div class="row-fluid">
              <h2 class="m-0 text-inverse"><strong><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'INVOICE'?> </strong></h2>
          </div>
          <small><?php if (!empty($model->user->userLocation->location->address)): ?>
                <?= $model->user->userLocation->location->address?>
			<?php endif; ?>
			<?php if (!empty($model->user->userLocation->location->phone_number)): ?><br>
            <?= $model->user->userLocation->location->phone_number?>
			<?php endif; ?> 
      </small> 
      </div>
      <div class="clearfix"></div>
          </h2>
        </div>
        <!-- /.col -->
      </div>
    <div class="row invoice-info m-t-20">
        <!-- /.col -->
		<?php if (!empty($customer)):?>
        <div class="col-sm-8 invoice-col m-b-20 p-0">
          <div class="row m-t-10">
            <div class="col-xs-8">
                <h4 class="m-0 f-w-400">
                    <a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => 'customer', 'id' => $customer->id, '#' => 'student']) ?>">
                        <strong><?= isset($customer->publicIdentity) ? $customer->publicIdentity : null?></strong>
                    </a>
                </h4>
              <div class="text-gray">
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
                ?>
              <?php 
                    echo $billingAddress->address.'<br> '.$billingAddress->city->name.', ';
                echo $billingAddress->province->name.'<br>'.$billingAddress->country->name.' ';
                echo $billingAddress->postal_code;
            } ?>
            <div class="row-fluid m-t-20">
               <?php if (!empty($customer->email)): ?>
               <?= 'E: '; ?><?= $customer->email?>
               <?php endif; ?>
            </div>
            <!-- Phone number -->
            <div class="row-fluid">
              <?php if (!empty($phoneNumber)) {
                ?><?= 'P: '; ?>
              <?= $phoneNumber->number;
            } ?>
            </div>
            </div></div>
          </div>
        </div>
		<?php endif; ?>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col m-t-10 text-right p-0">
            <div class="row-fluid  text-gray">
              <div class="col-md-4 pull-right text-left p-r-0 p-l-30"><?= '#'.$model->getInvoiceNumber()?></div>
              <div class="col-md-2 pull-right"><?= 'Number:'?> </div> 
              <div class="clearfix"></div>
            </div>
          <div class="row-fluid text-gray">
              <div class="col-md-4 pull-right text-left p-r-0 p-l-30"><?= Yii::$app->formatter->asDate($model->date); ?></div>
              <div class="col-md-2 pull-right">Date:</div>
              <div class="clearfix"></div>
          </div>
          <div  class="row-fluid text-gray">
				  <div id="invoice-status" class="col-md-4 pull-right text-left p-r-0 p-l-30">
				  <?= $model->getStatus(); ?></div>
				  <div class="col-md-2 pull-right">Status:</div>
              <div class="clearfix"></div>
            </div>
          </div>
	<?php if((empty($model->lineItem) || $model->lineItem->isOtherLineItems()) && $model->isInvoice()) :?>
	<div id="add-misc-item" class="col-md-12">
    <div class="row m-b-20">
	<a href="#" class="add-new-misc text-add-new"><i class="fa fa-plus-circle"></i> Add Misc</a>
	<div class="clearfix"></div>
    </div>
	</div>
    <?php endif; ?>
    <?php if(!empty($model->lineItem) && (!$model->lineItem->isOpeningBalance())) :?>
    <div id="apply-discount" class="col-md-12">
    <div class="row m-b-20">
	<a href="#" class="add-new-misc text-add-new"><i class="fa fa-plus-circle"></i> Apply Discount</a>
	<div class="clearfix"></div>
    </div>
	</div>
    <?php endif; ?>
	<?php echo $this->render('_line-item', [
        'invoiceModel' => $model,
    ]) ?>
	<?php echo $this->render('_view-line-item', [
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
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
<div class="row no-print">
  <div class="col-xs-12">
  <!-- <hr class="default-hr">   -->
  </div>
</div>
</div>
<div class="reminder_notes">
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
		$.pjax.reload({container : '#line-item-listing', timeout : 4000});
        invoice.updateSummarySectionAndStatus();
    },
}
$(document).ready(function() {
	$('#add-misc-item').click(function(){
    $('input[type="text"]').val('');
    $('.tax-compute').hide();
    $('#invoicelineitem-tax_status').val('');
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
