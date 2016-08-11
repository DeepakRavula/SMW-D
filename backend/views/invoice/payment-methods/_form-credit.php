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

<div class="payments-form p-l-20">
    <?php $form = ActiveForm::begin(); ?>
 	<div class="row">
        <div class="col-xs-3">
    		<?php echo $form->field($model, 'credit')->textInput()->label('Available Credit')?>
        </div>
        <div class="col-xs-3">
   			<?php echo $form->field($model, 'amount')->textInput()->label('Amount Needed') ?>
        </div>
		<div class="col-xs-3">
   			<?php echo $form->field($model, 'amount')->textInput()->label('Amount To Apply') ?>
        </div>
	</div>
	<?php echo $form->field($model, 'sourceType')->hiddenInput()->label(false); ?>
	<?php echo $form->field($model, 'sourceId')->hiddenInput()->label(false); ?>
	<?php echo $form->field($model, 'payment_method_id')->hiddenInput()->label(false); ?>
    <div class="form-group p-l-20">
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
			<?php 
			if(! $model->isNewRecord){
				echo Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	
			}
		?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
