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
        <div class="col-md-5">
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
        <div class="col-md-2">
            <?= $form->field($model, 'cost')->textInput();?>
        </div>
        
        <div class="col-md-2">
            <?= $form->field($model, 'unit')->textInput(['id' => 'unit-line']);?>
        </div>
        
        <div class="col-md-3">
            <?= $form->field($model, 'amount')->textInput(['id' => 'amount-line'])->label('Base Price');?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'grossPrice')->textInput(['readOnly' => true, 
                'value' => Yii::$app->formatter->asDecimal($model->grossPrice, 4)])->label('Gross Price');?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'netPrice')->textInput(['readOnly' => true, 
                'value' => Yii::$app->formatter->asDecimal($model->netPrice, 4)])->label('Net Price');?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'itemTotal')->textInput(['readOnly' => true,
                'value' => Yii::$app->formatter->asDecimal($model->itemTotal, 4)])->label('Total');?>
        </div>
	<div class="col-md-12">
            <?= $form->field($model, 'description')->textarea();?>
        </div>
        
           <div class="col-md-12">
    <div class="form-group pull-right">
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default line-item-cancel']);?>
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
    </div>
<div class="form-group pull-left">       
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
</div>
        <div class="clearfix"></div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>

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
                    'amount' : $('#amount-line').val()
                }),
                success: function(response) {
                    $('#invoicelineitem-netprice').val(response.netPrice);
                    $('#invoicelineitem-grossprice').val(response.grossPrice);
                    $('#invoicelineitem-itemtotal').val(response.itemTotal);
                }
            });
        }
    };

    $(document).on("change", '#amount-line, #unit-line', function() {
        lineItem.computeNetPrice();
        return false;
    });
</script>
