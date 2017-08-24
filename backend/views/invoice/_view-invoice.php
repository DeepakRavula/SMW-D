<?php
use yii\helpers\Html;
use backend\models\search\InvoiceSearch;
use yii\helpers\Url;
use common\models\User;
use yii\widgets\ActiveForm;
use kartik\editable\Editable;

?>
<div id="invoice-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<section class="invoice">
<div class="row">
	<div class="col-xs-12">
	  <h2 class="page-header" style="margin: -11px 0 10px 0;">
		<img style="width:110px" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />    
		<small class="pull-right">Date: <?= Yii::$app->formatter->asDate($model->date); ?></small>
	  </h2>
	</div>
	<!-- /.col -->
</div>
 <div class="row invoice-info">
	<div class="col-sm-5 invoice-col">
	  From
	  <address>
		<strong>Arcadia Music Academy (<?= $model->location->name; ?>)</strong><br>
		<?php if (!empty($model->location->address)): ?>
			<?= $model->location->address;?>
		<?php endif; ?><br>
		<?php if (!empty($model->location->city_id)): ?>
			<?= $model->location->city->name;?>
		<?php endif; ?>
		<?php if (!empty($model->location->province_id)): ?>
			<?= ', ' . $model->location->province->name;?>
		<?php endif; ?><br>
		<?php if (!empty($model->location->postal_code)): ?>
			<?= $model->location->postal_code;?>
		<?php endif; ?><br>
		<?php if (!empty($model->location->phone_number)): ?>
			Phone: <?= $model->location->phone_number?>
		<?php endif; ?><br>
		 <?php if (!empty($model->location->email)): ?>
			Email: <?= $model->location->email?>
		<?php endif; ?><br>
		www.arcadiamusicacademy.com
	  </address>
	</div>
	<!-- /.col -->
    <?php if (!empty($customer)):?>
	<div class="col-sm-3 invoice-col">
	  To
	  <address>
		<strong><?php 
		if(!$model->isUnassignedUser()) {
			$roles = Yii::$app->authManager->getRolesByUser($model->user_id);
			$role = end($roles);
		} ?>
		<?php if(!empty($role) && $role->name === User::ROLE_CUSTOMER) : ?>
		  <a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => 'customer', 'id' => $customer->id]) ?>">
		<?php endif; ?>
		<?= isset($customer->publicIdentity) ? $customer->publicIdentity : null?>
		</a></strong><br>
        <?php if (!empty($customer->billingAddress)) : ?>
		<?= $customer->billingAddress->address; ?><br>
		<?= $customer->billingAddress->city->name . ', ' . $customer->billingAddress->province->name; ?><br>
		<?= $customer->billingAddress->postal_code; ?><br>
		<?php endif; ?>
		<?php if (!empty($customer->phoneNumber)): ?>
		  Phone: <?= $customer->phoneNumber->number?><br>
		<?php endif; ?>
		<?php if (!empty($customer->email)): ?>
		  Email: <?= $customer->email?><br>
		<?php endif; ?>
	  </address>
	</div>
	<?php endif; ?>
	<!-- /.col -->
	<div class="col-sm-4 invoice-col">
	  <b>Invoice #<?= $model->getInvoiceNumber();?></b><br>
	  <br>
	  <b>Date:</b> <?= Yii::$app->formatter->asDate($model->date); ?><br>
	  <?php if (!$model->isInvoice()) : ?>
	  <b>Due Date:</b> <?= Yii::$app->formatter->asDate($model->dueDate); ?><br>
	  <?php endif; ?>
	  <b>Status:</b> <?= $model->getStatus(); ?>
	</div>
	<!-- /.col -->
  </div>
<div class="row">
	<?php if((empty($model->lineItem) || $model->lineItem->isOtherLineItems()) && $model->isInvoice()) :?>
	<a href="#" class="add-new-misc text-add-new m-r-10"><i class="fa fa-plus-circle"></i> Add Item</a>
<?php endif; ?>
	<?php if(!empty($model->lineItem) && ($model->lineItem->isOtherLineItems())) :?>
	<a href="#" class="apply-discount text-add-new"><i class="fa fa-plus-circle"></i> Apply Discount</a>
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
</div>
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
<div class="reminder_notes text-muted well well-sm no-shadow">
    <?php echo $model->reminderNotes; ?>
</div>
</section>
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

    $('.apply-discount').click(function(){
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
