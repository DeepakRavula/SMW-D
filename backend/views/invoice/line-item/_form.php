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
        <div class="col-md-4">
            <?= $form->field($model, 'code')->textInput();?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'unit')->textInput(['id' => 'unit-line']);?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'cost')->textInput();?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'amount')->textInput(['id' => 'amount-line'])->label('Base Price');?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'tax_status')->dropDownList(ArrayHelper::map(
                            TaxStatus::find()->all(), 'id', 'name'
            ), ['prompt' => 'Select', 'id' => 'lineitem-tax_status']);?>
        </div>
        <div class="col-xs-2">
            <?php echo $form->field($model, 'taxPercentage')->textInput(['readonly' => true])->label('Tax (%)') ?>
        </div>
        <div class="col-xs-3">
            <?php echo $form->field($model, 'tax_rate')->textInput(['readonly' => true, 'id' => 'lineitem-tax_rate'])?>
        </div>
	   <div class="col-md-3">
            <?= $form->field($model, 'isRoyalty')->widget(SwitchInput::classname(),
                [
                'name' => 'isRoyalty',
                'pluginOptions' => [
                    'handleWidth' => 30,
                    'onText' => 'Yes',
                    'offText' => 'No',
                ],
            ]);?>
        </div>
	   <div class="clearfix"></div>
	   
        <div class="col-md-3">
            <?= $form->field($model, 'discount')->textInput()->label('Discount');?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'discountType')->widget(SwitchInput::classname(),
                [
                'name' => 'discountType',
                'pluginOptions' => [
                    'handleWidth' => 30,
                    'onText' => '%',
                    'offText' => '$',
                ],
            ])->label('Discount Type');?>
        </div>
       <div class="col-md-3">
            <?= $form->field($model, 'netPrice')->textInput(['readOnly' => true])->label('Net Price');?>
        </div> 
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea();?>
        </div>
    <div class="col-md-12 p-l-20 form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
        
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default line-item-cancel']);?>
        <?= Html::a('Delete', [
            'delete', 'id' => $model->id
        ],
        [
            'class' => 'btn btn-primary',
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
    $(document).on("change", '#amount-line', function() {
        computeNetPrice();
        return false;
    });
    
    $(document).on("change", '#invoicelineitem-discount', function() {
        computeNetPrice();
        return false;
    });

    $('input[name="InvoiceLineItem[discountType]"]').on('switchChange.bootstrapSwitch', function() {
        computeNetPrice();
        return false;
    });
    
    $(document).on("change", '#lineitem-tax_status', function() {
        computeNetPrice();
        return false;
    });
    
    function computeNetPrice()
    {
        $.ajax({
            url: "<?php echo Url::to(['invoice-line-item/compute-net-price', 'id' => $model->id]); ?>",
            type: "POST",
            contentType: 'application/json',
            dataType: "json",
            data: JSON.stringify({
                'amount' : $('#amount-line').val(),
		'discount' : $('#invoicelineitem-discount').val(),
                'discountType' : $('input[name="InvoiceLineItem[discountType]"]').is(":checked"),
                'taxStatus' : $('#lineitem-tax_status').val()
            }),
            success: function(response) {
                $('#invoicelineitem-netprice').val(response.netPrice);
                $('#invoicelineitem-taxpercentage').val(response.taxPercentage);          
                $('#lineitem-tax_rate').val(response.taxRate);          
            },
            error: function() {
            }
        });	
    }
</script>
