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
<?php $message = 'Warning: You have entered a non-approved Arcadia discount. All non-approved discounts must be submitted in writing and approved by Head Office prior to entering a discount, otherwise you are in breach of your agreement.'; ?>
<script>
$(document).on('beforeSubmit', '#apply-discount-form', function () {
	var message = '<?= $message;?>';
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
				$('#invoice-discount-warning').html(message).fadeIn().delay(8000).fadeOut();
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