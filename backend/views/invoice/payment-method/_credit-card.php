<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;
use common\models\Invoice;
use common\models\Allocation;
use common\models\BalanceLog;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>


<div class="payments-form p-l-20 form-well-smw form-well m-t-10 m-b-0">
  <h4 class="m-t-0 m-b-20">Credit Card payment</h4>
    <?php $form = ActiveForm::begin(); ?>
 	<div class="row">
        <div class="col-xs-3">
			<?php
				$amount = '0.00';
				if($invoice->total > $invoice->invoicePaymentTotal){
					$amount = $invoice->invoiceBalance;
				}
			?>
   			<?php echo $form->field($model, 'amount')->textInput(['value' => $amount])->label('Amount Needed') ?>
        </div>
		<?php echo $form->field($model, 'payment_method_id')->hiddenInput(['class' => 'payment-method-id'])->label(false); ?>
	</div>
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
