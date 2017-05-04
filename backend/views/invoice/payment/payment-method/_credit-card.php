<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>


<div class="payments-form p-l-20 form-well-smw form-well m-t-10 m-b-0">
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['payment/invoice-payment', 'id' => $invoice->id]),
    ]); ?>
 	<div class="row">
        <div class="col-xs-3">
   			<?php echo $form->field($model, 'amount')->textInput(['value' => $amount])->label('Amount Needed') ?>
        </div>
		<div class="col-xs-3">
   			<?php echo $form->field($model, 'reference')->textInput()->label('Reference Number') ?>
        </div>
		<?php echo $form->field($model, 'payment_method_id')->hiddenInput(['class' => 'payment-method-id'])->label(false); ?>
	</div>
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
