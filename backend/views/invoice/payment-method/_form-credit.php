<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="payments-form p-l-20 form-well-smw form-well m-t-10 m-b-0">
    <?php $form = ActiveForm::begin([
        'id' => 'apply-credit-form',
        'action' => Url::to(['payment/credit-payment', 'id' => $invoice->id]),
    ]); ?>
 	<div class="row">
        <div class="col-xs-3">
    		<?php echo $form->field($model, 'credit')->textInput()->label('Available Credit')?>
        </div>
        <div class="col-xs-3">			
   			<?php echo $form->field($model, 'amountNeeded')->textInput()->label('Amount Needed') ?>
        </div>
		<div class="col-xs-3">
   			<?php echo $form->field($model, 'amount')->textInput()->label('Amount To Apply') ?>
        </div>
	</div>
	<?php echo $form->field($model, 'sourceType')->hiddenInput()->label(false); ?>
	<?php echo $form->field($model, 'sourceId')->hiddenInput()->label(false); ?>
	<?php echo $form->field($model, 'payment_method_id')->hiddenInput(['class' => 'payment-method-id'])->label(false); ?>
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
			<?php 
            if (!$model->isNewRecord) {
                echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
            }
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
