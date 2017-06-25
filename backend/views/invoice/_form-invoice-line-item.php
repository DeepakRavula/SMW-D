<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\TaxStatus;
use common\models\ItemCategory;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div id="invoice-line-item-modal" class="invoice-line-item-form">
    <?php $form = ActiveForm::begin([
        'id' => 'add-misc-item-form',
        'action' => Url::to(['invoice/add-misc', 'id' => $invoiceModel->id]),
    ]); ?>
    <div class="row">
        <div class="col-xs-6">
            <?php echo $form->field($model, 'itemCategoryId')->dropDownList(
                    ArrayHelper::map(ItemCategory::find()
                        ->notDeleted()
                        ->active()
                        ->all(), 'id', 'name'), ['prompt' => 'Select Category']) ?>
        </div>
        <div class="col-xs-6">
            <?php echo $form->field($model, 'item_id')->widget(DepDrop::classname(),
                [
                'options' => ['id' => 'invoicelineitem-itemid'],
                'pluginOptions' => [
                        'depends' => ['invoicelineitem-itemcategoryid'],
                        'placeholder' => 'Select Item',
                        'url' => Url::to(['item-category/items']),
                ],
            ])?>
        </div>
        <div class="col-xs-8">
            <?php echo $form->field($model, 'description')->textInput()?>
        </div>
        <div class="col-xs-2">
            <?php echo $form->field($model, 'unit')->textInput()?>
        </div>
        <div class="col-xs-2">
            <?php echo $form->field($model, 'amount')->textInput()?>
        </div>
        <div class="col-xs-8">
            <?php echo $form->field($model, 'isRoyaltyExempted')->checkbox()?>
        </div>
    </div>
    <div class="row tax-compute">
        <div class="col-xs-3">
            <?php echo $form->field($model, 'tax_type')->textInput(['readonly' => true])?>
        </div>
        <div class="col-xs-2">
               <?php echo $form->field($model, 'tax_code')->textInput(['readonly' => true])?>
        </div>
        <div class="col-xs-2">
            <?php echo $form->field($model, 'tax')->textInput(['readonly' => true])?>
        </div>
        <div class="col-xs-2">
            <?php echo $form->field($model, 'tax_rate')->textInput(['readonly' => true])?>
        </div>
        <div class="col-xs-3">
            <?= Html::a('Calculate Tax', '', ['class' => 'btn btn-success btn-xs m-t-30 calculate-tax-button']);?>
        </div>
    </div>

    <div class="row misc-tax-status">
        <div class="col-xs-4">
            <?php echo $form->field($model, 'tax_status')->dropDownList(ArrayHelper::map(
                        TaxStatus::find()->all(), 'id', 'name'), ['prompt' => 'Select'])
            ?>
        </div>
        <div class="col-xs-4">
            <?php echo $form->field($model, 'code')->textInput() ?>
        </div>
    </div>
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default add-misc-cancel']);?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.calculate-tax-button').click(function(){
        var amount = $('#invoicelineitem-amount').val();
        var tax = $('#invoicelineitem-tax').val();
        if(tax == '') {
            var tax = 5;
        }
        $.ajax({
            url: "<?php echo Url::to(['invoice-line-item/compute-tax']); ?>",
            type: "POST",
            contentType: 'application/json',
            dataType: "json",
            data: JSON.stringify({
                    'amount' :amount,
                    'tax' : tax,
            }),
            success: function(response) {
                var response =  jQuery.parseJSON(JSON.stringify(response));
                $('#invoicelineitem-tax_rate').val(response);
            }
        });
        return false;
    });
    $('#invoicelineitem-itemid').change(function(){
        var params   = $.param({ itemId: $('#invoicelineitem-itemid').val() });
        $.ajax({
            url: "<?php echo Url::to(['item-category/get-item-values'])?>?" + params,
            type: "POST",
            contentType: 'application/json',
            dataType: "json",
            success: function(response) {
                $('#invoicelineitem-description').val(response.description);
                $('#invoicelineitem-amount').val(response.price);
                $('#invoicelineitem-code').val(response.code);
                $('#invoicelineitem-unit').val(1);
                $('#invoicelineitem-tax_status').val(response.tax);
                if (response.royaltyFree) {
                    $('#invoicelineitem-isroyaltyexempted').prop('checked', true);
                } else {
                    $('#invoicelineitem-isroyaltyexempted').prop('checked', false);
                }
                changeTax();
            }
        });
        return false;
    });
    $('#invoicelineitem-tax_status').change(function(){
        changeTax();
    });
});
    function changeTax() {
        var taxStatusId = $('#invoicelineitem-tax_status').val();
        if(taxStatusId && parseInt(taxStatusId) === 2){
            $('.tax-compute').show();
            $('#invoicelineitem-tax_type').val('NO TAX');
            $('#invoicelineitem-tax_code').val('ON');
            $('#invoicelineitem-tax_rate').val(0.00);
            $('#invoicelineitem-tax').val(0.00);
        } else {
            var amount = $('#invoicelineitem-amount').val();
            var taxStatusName = $(this).children("option").filter(":selected").text();
            $.ajax({
                url: "<?php echo Url::to(['invoice/compute-tax']); ?>",
                type: "POST",
                contentType: 'application/json',
                dataType: "json",
                data: JSON.stringify({
                        "amount":amount,
                        "taxStatusName":taxStatusName,
                        "taxStatusId":taxStatusId,
                }),
                success: function(response) {
                    var response =  jQuery.parseJSON(JSON.stringify(response));
                    $('.tax-compute').show();
                    $('#invoicelineitem-tax_type').val(response.tax_type);
                    $('#invoicelineitem-tax_code').val(response.code);
                    $('#invoicelineitem-tax').val(response.tax);
                    $('#invoicelineitem-tax_rate').val(response.rate);
                }
            });
        }
    }
</script>
<script>
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
				$.pjax.reload({container : '#line-item-listing', timeout:6000});
				$('input[name="Payment[amount]"]').val(response.amount);
                invoice.updateSummarySectionAndStatus();
				$('#invoice-line-item-modal').modal('hide');
			}else
			{
			 $(this).yiiActiveForm('updateMessages', response.errors, true);
			}
		}
		});
		return false;
});
</script>