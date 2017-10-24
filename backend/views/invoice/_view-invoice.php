<?php
use yii\helpers\Html;
use backend\models\search\InvoiceSearch;
use yii\helpers\Url;
use common\models\User;
use yii\widgets\ActiveForm;
use kartik\editable\Editable;

?>
<div id="invoice-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div style="margin-bottom: 10px">
    <?php yii\widgets\Pjax::begin([
		'id' => 'invoice-button-listing',
		'timeout' => 6000,
	]) ?>
	<?php if((empty($model->lineItem) || $model->lineItem->isOtherLineItems()) && $model->isInvoice()) :?>
	<?= Html::a('Add Item', '#', ['class' => 'add-new-misc btn btn-primary btn-sm m-r-10']) ?>
<?php endif; ?>
	<?php if(!empty($model->lineItem) && ($model->lineItem->isOtherLineItems())) :?>
	 <?= Html::a('Apply Discount', '#', ['class' => 'apply-discount btn btn-primary btn-sm']) ?>
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
    <?php \yii\widgets\Pjax::end(); ?>	
	<?php echo $this->render('_line-item', [
        'invoiceModel' => $model,
    ]) ?>
</div>
<div>
	<?php echo $this->render('_view-line-item', [
		'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
		'searchModel' => $searchModel,
	]) ?>	
</div>
    <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
          <div id="invoice-summary-section" class="table-responsive">
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
                      <?php
                      echo $this->render('_view-bottom-summary', [
                          'model' => $model,
                      ]);

                      ?>	

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
        $.pjax.reload({url:url,container:"#line-item-listing",replace:false,  timeout: 4000});  //Reload GridView
        $('#show-column').hide();
        $('#hide-column').toggle();
    });

    $(document).on('click', '#hide-column' ,function(){
        var url = "<?php echo Url::to(['invoice/view', 'id' => $model->id]); ?>&InvoiceSearch[toggleAdditionalColumns]="  + 0;
        $.pjax.reload({url:url,container:"#line-item-listing",replace:false,  timeout: 4000});  //Reload GridView
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
                invoice.updateSummarySectionAndStatus();
				$('#invoice-line-item-modal').modal('hide');
                $.pjax.reload({container: "#invoice-button-listing", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#line-item-listing", replace: false, async: false, timeout: 6000});
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