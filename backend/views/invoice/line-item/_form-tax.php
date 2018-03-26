<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\TaxStatus;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
    .col-sm-6 {
        width: 70%;
    }
</style>
<div class="lesson-qualify p-10">
<div id="edit-tax-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
        'id' => 'edit-tax-form',
    'enableClientValidation' => false
]); ?>
    <div id="tax-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <label class="dollar-symbol tax-status">Tax Status</label>
        </div>
        <div class="col-xs-8 pull-right">
            <?= $form->field($model, 'tax_status')->dropDownList(ArrayHelper::map(
                    TaxStatus::find()->all(),
    'name',
    'name'
            ), ['prompt' => 'Select', 'id' => 'lineitem-tax_status'])->label(false);?>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-xs-4">
            <label class="tax-status">Tax Rate</label>
        </div>
        <div class="col-xs-8 pull-right">
            <label class="tax-rate"><?= $model->taxPercentage; ?></label><label class="tax-dollar-symbol">%</label>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>
<script>
    var lineItem = {
        fetchTaxPercentage : function() {
            var taxStatusId = $('#lineitem-tax_status').val();
            if ($.isEmptyObject(taxStatusId)) {
                $('.tax-rate').text('0');
            } else {
                $.ajax({
                    url: "<?php echo Url::to(['invoice-line-item/fetch-tax-percentage']); ?>?taxStatusId=" + taxStatusId,
                    type: 'GET',
                    contentType: 'application/json',
                    dataType: "json",
                    success: function(response) {
                        $('.tax-rate').text(response);
                    }
                });
            }
        }
    };
 $(document).ready(function() { 
 lineItem.fetchTaxPercentage();
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
        $('#edit-tax-error-notification').html('Please select the tax-status!').fadeIn().delay(5000).fadeOut();
        return false;
    }
    $('#tax-spinner').show();
    var selectedRows = $('#line-item-grid').yiiGridView('getSelectedRows');
    var params = $.param({ 'InvoiceLineItem[ids]' : selectedRows });
    $.ajax({
        url    : '<?= Url::to(['invoice-line-item/edit-tax']) ?>?' + params,
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
                $('input[type="checkbox"]').trigger('change');
                $('#success-notification').html(response.message).fadeIn().delay(5000).fadeOut();
            } else {
                $('#invoice-error-notification').html(response.message).fadeIn().delay(5000).fadeOut();
            }
        }
    });
    return false;
});
</script> 