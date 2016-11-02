<?php
use yii\helpers\Html;
use backend\models\search\InvoiceSearch;
use yii\helpers\Url;
use common\models\Invoice;
use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;

?>
<div class="invoice-view p-50">
	<div class="pull-right">
		<?php if ((int) $model->type === Invoice::TYPE_PRO_FORMA_INVOICE) : ?>
			<?php $form = ActiveForm::begin([
                'id' => 'mail-flag',
            ]); ?>
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
		<?php ActiveForm::end(); ?>
	<?php endif; ?>
	</div>
         <div class="row">
		<div class="col-xs-12 p-0">
          <h2 class="m-0">
            <a href="<?= Yii::getAlias('@frontendUrl') ?>" class="logo pull-left">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
		<?= Html::a('<i class="fa fa-envelope-o"></i> Mail this Invoice', ['send-mail', 'id' => $model->id], ['class' => 'btn btn-default pull-right  m-l-20']) ?>  
          <?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default pull-right', 'target' => '_blank']) ?>
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
              <h4 class="m-0 f-w-400"><strong><?= isset($customer->publicIdentity) ? $customer->publicIdentity : null?></strong></h4>
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
              <div class="col-md-4 pull-right text-right p-r-0"><?= '#'.$model->getInvoiceNumber()?></div>
              <div class="col-md-2 pull-right"><?= 'Number:'?> </div> 
              <div class="clearfix"></div>
            </div>
          <div class="row-fluid text-gray">
              <div class="col-md-4 pull-right text-right p-r-0"><?= Yii::$app->formatter->asDate($model->date); ?></div>
              <div class="col-md-2 pull-right">Date:</div>
              <div class="clearfix"></div>
          </div>
          <div  class="row-fluid text-gray">
				  <div id="invoice-status" class="col-md-4 pull-right text-right p-r-0">
				  <?= $model->getStatus(); ?></div>
				  <div class="col-md-2 pull-right">Status:</div>
              <div class="clearfix"></div>
            </div>
          </div>
	<div id="add-misc-item" class="col-md-12">
    <div class="row m-b-20">
	<a href="#" class="add-new-misc text-add-new"><i class="fa fa-plus-circle"></i> Add Misc</a>
	<div class="clearfix"></div>
  </div>
	</div>
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
                    <?php if (!empty($model->notes)):?>
                    <div class="row-fluid m-t-20">
                      <em><strong>Printed Notes: </strong><Br>
                        <?= $model->notes; ?></em>
                      </div>
                      <?php endif; ?>
                      <?php if (!empty($model->notes) && !empty($model->internal_notes)):?>
                      <hr class="right-side-faded">
                      <?php endif; ?>
                      <?php if (!empty($model->internal_notes)):?>
                      <div class="row-fluid">
                      <em><strong>Internal notes:</strong><Br> <?= $model->internal_notes; ?></em>
                    </div>
                    <?php endif; ?>
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
  </div>
</div>
</div>
<div>
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
