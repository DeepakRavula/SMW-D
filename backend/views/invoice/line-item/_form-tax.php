<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\TaxStatus;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-qualify p-10">
<div id="edit-tax-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
    'id' => 'edit-tax-form',
	'enableClientValidation' => true
]); ?>
    <div id="tax-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
   <div class="row">
		<?= $form->field($model, 'tax_status')->dropDownList(ArrayHelper::map(
			TaxStatus::find()->all(), 'name', 'name'
		), ['prompt' => 'Select', 'id' => 'lineitem-tax_status']);?>
            <?php echo $form->field($model, 'taxPercentage', [
					'inputTemplate' => '<div class="input-group">'
					. '{input}<span class="input-group-addon">%</span></div>'])->textInput(['readonly' => true, 'class' => 'right-align form-control'])->label('Tax Rate') ?>
           
	</div>
	<?php ActiveForm::end(); ?>

<script>
    var lineItem = {
        fetchTaxPercentage : function() {
            var taxStatusId = $('#lineitem-tax_status').val();
            if ($.isEmptyObject(taxStatusId)) {
                $('#invoicelineitem-taxpercentage').val(0);
            } else {
                $.ajax({
                    url: "<?php echo Url::to(['invoice-line-item/fetch-tax-percentage']); ?>?taxStatusId=" + taxStatusId,
                    type: 'GET',
                    contentType: 'application/json',
                    dataType: "json",
                    success: function(response) {
                        $('#invoicelineitem-taxpercentage').val(response);
                    }
                });
            }
        },
    };
 $(document).ready(function() { 
 	$('#line-item-grid').on('change', '.lineItemId', function(){
 		var checked = $(this).prop('checked');
 		if(checked) {
 			$("#edit-tax").removeAttr('disabled');
        	$('#edit-tax').unbind('click', false);	
 		} else {
			$("#edit-tax").attr("disabled", true);
        	$('#edit-tax').bind('click', false);
 			
 		}
 	});
    $(document).on("change", '#lineitem-tax_status', function() {
        lineItem.fetchTaxPercentage();
        return false;
    });
});
$(document).off('click', '.edit-tax-save').on('click', '.edit-tax-save', function () {
    var tax = $('#lineitem-tax_status').val();
    if ($.isEmptyObject(tax)) {
        $('#edit-tax-error-notification').html('Please fix the error!').fadeIn().delay(5000).fadeOut();
        return false;
    }
    $('#tax-spinner').show();
    var selectedRows = $('#line-item-grid').yiiGridView('getSelectedRows');
    var params = $.param({ 'InvoiceLineItem[ids]' : selectedRows });
    $.ajax({
        url    : '<?= Url::to(['tax-status/edit-line-item-tax']) ?>?' + params,
        type   : 'post',
        dataType: "json",
        data   : $('#edit-tax-form').serialize(),
        success: function(response)
        {
            if(response.status)
            {
                $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-header-summary", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-view-lineitem-listing", replace: false, async: false, timeout: 6000});
                $('#tax-spinner').hide();
                $('#edit-tax-modal').modal('hide');
                $('#success-notification').html(response.message).fadeIn().delay(5000).fadeOut();
            }
        }
    });
    return false;
});
</script> 