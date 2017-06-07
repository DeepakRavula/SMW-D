<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Invoice;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div id="apply-discount-modal" class="apply-discount-form">
    <?php $form = ActiveForm::begin([
        'id' => 'apply-discount-form',
        'action' => Url::to(['invoice-line-item/apply-discount', 'id' => $invoiceModel->id]),
    ]); ?>
 	<div class="row">
        <div class="col-xs-3">
    		<?php $invoiceModel->setScenario(Invoice::SCENARIO_DISCOUNT);
            echo $form->field($invoiceModel, 'discountApplied')->textInput()->hint('%') ?>
        </div>
    </div>

    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
$(document).on('beforeSubmit', '#apply-discount-form', function () {
	$.ajax({
		url    : $(this).attr('action'),
		type   : 'post',
		dataType: "json",
		data   : $(this).serialize(),
		success: function(response)
		{
		   if(response.status)
		   {
				$.pjax.reload({container : '#line-item-listing', async:false});
				$('input[name="Payment[amount]"]').val(response.amount);
				$.pjax.reload({container : '.payment-method-section', async:false});
                invoice.updateSummarySectionAndStatus();
				$('#apply-discount-modal').modal('hide');
			}else
			{
			 $(this).yiiActiveForm('updateMessages',
				   response.errors
				, true);
			}
		}
		});
		return false;
});
</script>