<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\TaxStatus;
use common\models\ItemCategory;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\switchinput\SwitchInput;

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
        <div class="col-xs-6">
            <?php echo $form->field($model, 'code')->textInput() ?>
        </div>
        <div class="col-xs-3">
            <?php echo $form->field($model, 'royaltyFree')->widget(SwitchInput::classname(),
                [
                'name' => 'royaltyFree',
				'options' => [
					'id' => 'line-item-royalty-free',
				],
                'pluginOptions' => [
                    'handleWidth' => 30,
                    'onText' => 'Yes',
                    'offText' => 'No',
                ],
            ]);?>
        </div>
        <div class="col-xs-3">
            <?php echo $form->field($model, 'unit')->textInput()?>
        </div>
        <div class="col-xs-12">
            <?php echo $form->field($model, 'description')->textInput()?>
        </div>
        <div class="col-xs-3">
            <?php echo $form->field($model, 'amount')->textInput()->label('Base Price') ?>
        </div>
        <div class="col-xs-3">
            <?php echo $form->field($model, 'crossPrice')->textInput([
                'readonly' => true, 'id' => 'lineitem-crossprice'
                ])->label('Cross Price') ?>
        </div>
        <div class="col-xs-3">
            <?php echo $form->field($model, 'netPrice')->textInput([
                'readonly' => true, 'id' => 'lineitem-netprice'
                ])->label('Net Price') ?>
        </div>
        <div class="col-xs-3">
            <?php echo $form->field($model, 'itemTotal')->textInput([
                'readonly' => true, 'id' => 'lineitem-itemtotal'
                ])->label('Total') ?>
        </div>
    </div>
    <div class="row misc-tax-status">
        <div class="col-xs-4">
            <?php echo $form->field($model, 'tax_status')->dropDownList(ArrayHelper::map(
                        TaxStatus::find()->all(), 'id', 'name'), ['prompt' => 'Select'])
            ?>
        </div>
        <div class="col-xs-2">
            <?php echo $form->field($model, 'tax')->textInput(['readonly' => true])?>
        </div>
        <div class="col-xs-2">
            <?php echo $form->field($model, 'tax_rate')->textInput(['readonly' => true])?>
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
                $('#lineitem-crossprice').val(response.price);
                $('#lineitem-netprice').val(response.price);
                $('#invoicelineitem-tax_status').val(response.tax).trigger('change');
                if (response.royaltyFree) {
                    $('#line-item-royalty-free').bootstrapSwitch('state', true);
                } else {
                    $('#line-item-royalty-free').bootstrapSwitch('state', false);
                }
            }
        });
        return false;
    });
    $('#invoicelineitem-tax_status, #invoicelineitem-unit, #invoicelineitem-amount').change(function(){
        changeTax();
    });
});
    function changeTax() {
        var taxStatusId = $('#invoicelineitem-tax_status').val();
        var amount = $('#invoicelineitem-amount').val();
        var unit = $('#invoicelineitem-unit').val();
        var taxStatusName = $(this).children("option").filter(":selected").text();
        $.ajax({
            url: "<?php echo Url::to(['invoice/compute-tax']); ?>",
            type: "POST",
            contentType: 'application/json',
            dataType: "json",
            data: JSON.stringify({
                "unit": unit,
                "amount":amount,
                "taxStatusName":taxStatusName,
                "taxStatusId":taxStatusId,
            }),
            success: function(response) {
                $('#invoicelineitem-tax').val(response.tax);
                $('#invoicelineitem-tax_rate').val(response.rate);
                $('#lineitem-crossprice').val(response.crossPrice);
                $('#lineitem-netprice').val(response.crossPrice);
                $('#lineitem-itemtotal').val(response.total);
            }
        });
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