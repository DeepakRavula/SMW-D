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
    		<?php echo $form->field($model, 'sourceId')->textInput(['readOnly' => true, 'class' => 'text-right form-control'])->label('Source')?>
        </div>
        <div class="col-xs-3">
    		<?php echo $form->field($model, 'credit')->textInput(['readOnly' => true, 'class' => 'text-right form-control'])->label('Available Credit')?>
        </div>
        <div class="col-xs-3">
   			<?php echo $form->field($model, 'amountNeeded')->textInput(['readOnly' => true, 'class' => 'text-right form-control'])->label('Amount Needed') ?>
        </div>
		<div class="col-xs-3">
   			<?php echo $form->field($model, 'amount')->textInput(['readOnly' => true, 'class' => 'text-right form-control'])->label('Amount To Apply') ?>
        </div>
	</div>
    <div class="form-group pull-right">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default apply-credit-cancel']);
        ?>
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
			
    </div>

    <?php ActiveForm::end(); ?>

</div>
