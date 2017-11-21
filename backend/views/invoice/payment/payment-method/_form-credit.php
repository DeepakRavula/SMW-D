<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-create-form row">
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
    <div class="form-group pull-right">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default apply-credit-cancel']);
        ?>
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
			
    </div>

    <?php ActiveForm::end(); ?>

</div>
