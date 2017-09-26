<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\switchinput\SwitchInput;
use common\models\TaxStatus;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-qualify p-10">

<?php $form = ActiveForm::begin([
    'id' => 'line-item-edit-form',
	'action' => Url::to(['invoice-line-item/update', 'id' => $model->id]),
	'enableClientValidation' => true
]); ?>
   <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'code')->textInput();?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'royaltyFree')->widget(SwitchInput::classname(),
                [
                'name' => 'royaltyFree',
                'pluginOptions' => [
                    'handleWidth' => 30,
                    'onText' => 'Yes',
                    'offText' => 'No',
                ],
            ]);?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'cost')->textInput();?>
        </div>
        <div class="col-md-4">
            <?php if (!$model->isLessonItem()) : ?>
                <?= $form->field($model, 'tax_status')->dropDownList(ArrayHelper::map(
                                TaxStatus::find()->all(), 'id', 'name'
                ), ['prompt' => 'Select', 'id' => 'lineitem-tax_status']);?>
            <?php else : ?> 
                <?= $form->field($model, 'tax_status')->textInput(['readOnly' => true]); ?>
            <?php endif; ?>
        </div>
        <div class="col-xs-4">
            <?php echo $form->field($model, 'taxPercentage')->textInput(['readonly' => true])->label('Tax (%)') ?>
        </div>
        <div class="col-xs-4">
            <?php echo $form->field($model, 'tax_rate')->textInput(['readonly' => true, 'id' => 'lineitem-tax_rate'])?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'unit')->textInput(['id' => 'unit-line']);?>
        </div>
        
        <div class="col-md-3">
            <?= $form->field($model, 'amount')->textInput(['id' => 'amount-line'])->label('Base Price');?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'netPrice')->textInput(['readOnly' => true])->label('Net Price');?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'itemTotal')->textInput(['readOnly' => true])->label('Total');?>
        </div>
	<div class="col-md-12">
            <?= $form->field($model, 'description')->textarea();?>
        </div>
        <div class="col-md-6">
            <?= $form->field($paymentFrequencyDiscount, 'value')->textInput()
                    ->label('Payment Frequency Discount(%)'); ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($customerDiscount, 'value')->textInput()
                            ->label('Customer Discount(%)'); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($lineItemDiscount, 'value')->textInput()
                                    ->label('Line Item Discount'); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($lineItemDiscount, 'valueType')->widget(SwitchInput::classname(),
                [
                'name' => 'valueType',
                'pluginOptions' => [
                    'handleWidth' => 30,
                    'onText' => '$',
                    'offText' => '%',
                ],
            ])->label('Discount Type');?>
        </div>
        <div class="col-md-5">
            <?= $form->field($multiEnrolmentDiscount, 'value')->textInput()
                    ->label('Multi Enrolment Discount($)'); ?>
        </div>
        
    <div class="col-md-12 p-l-20 form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
        
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default line-item-cancel']);?>
        <?= Html::a('Delete', [
            'delete', 'id' => $model->id
        ],
        [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ]
        ]); ?>
        <div class="clearfix"></div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>

<script>
    var lineItem = {
        computeNetPrice : function() {
            $.ajax({
                url: "<?php echo Url::to(['invoice-line-item/compute-net-price', 'id' => $model->id]); ?>",
                type: "POST",
                contentType: 'application/json',
                dataType: "json",
                data: JSON.stringify({
                    'unit' : $('#unit-line').val(),
                    'amount' : $('#amount-line').val(),
                    'taxStatus' : $('#lineitem-tax_status').val(),
                    'customerDiscount' : $('#customerlineitemdiscount-value').val(),
                    'paymentFrequencyDiscount' : $('#paymentfrequencylineitemdiscount-value').val(),
                    'multiEnrolmentDiscount' : $('#enrolmentlineitemdiscount-value').val(),
                    'lineItemDiscount' : $('#lineitemdiscount-value').val(),
                    'lineItemDiscountType' : $('input[name="LineItemDiscount[valueType]"]').is(":checked")
                }),
                success: function(response) {
                    $('#invoicelineitem-netprice').val(response.netPrice);
                    $('#invoicelineitem-taxpercentage').val(response.taxPercentage);
                    $('#lineitem-tax_rate').val(response.taxRate);
                    $('#invoicelineitem-itemtotal').val(response.itemTotal);
                }
            });	
        }
    };
    
    $(document).on("change", '#amount-line, #invoicelineitem-discount, #unit-line, \n\
        #lineitem-tax_status, #customerlineitemdiscount-value, #paymentfrequencylineitemdiscount-value, \n\
        #enrolmentlineitemdiscount-value, #lineitemdiscount-value', function() {
        lineItem.computeNetPrice();
        return false;
    });

    $('input[name="LineItemDiscount[valueType]"]').on('switchChange.bootstrapSwitch', function() {
        lineItem.computeNetPrice();
        return false;
    });
</script>
