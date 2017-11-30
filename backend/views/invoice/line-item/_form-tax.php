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
	'layout' => 'horizontal',
    'id' => 'edit-tax-form',
	'enableClientValidation' => true
]); ?>
   <div class="row">
		<?= $form->field($model, 'tax_status')->dropDownList(ArrayHelper::map(
			TaxStatus::find()->all(), 'id', 'name'
		), ['prompt' => 'Select', 'id' => 'lineitem-tax_status']);?>
            <?php echo $form->field($model, 'tax_rate', [
					'inputTemplate' => '<div class="input-group">'
					. '{input}<span class="input-group-addon">%</span></div>',])->textInput(['readonly' => true, 'class' => 'right-align form-control'])->label('Tax Rate') ?>
           <div class="col-md-12">
    <div class="form-group pull-right">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default edit-tax-cancel']);?>
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
    </div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>

<script>
    var lineItem = {
		fetchTaxPercentage : function() {
			var taxStatusId = $('#lineitem-tax_status').val();
			  $.ajax({
                url: "<?php echo Url::to(['invoice-line-item/fetch-tax-percentage']); ?>?taxStatusId=" + taxStatusId,
                type: 'GET',
                contentType: 'application/json',
                dataType: "json",
                success: function(response) {
                    $('#invoicelineitem-tax_rate').val(response);
                }
            });		
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
</script> 