<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;
use common\models\Invoice;
use common\models\Allocation;
use common\models\Payment;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="payments-form">
<?php
	$payment = Payment::find()
		->joinWith(['allocation a' => function($query){
			$query->where(['a.invoice_id' => Allocation::TYPE_OPENING_BALANCE]);
		}])
		->where(['user_id' => $invoiceModel->user_id])
		->one();
		$openingBalance = 0;
		if(! empty($payment)){
			$openingBalance = $payment->amount;
		}
	$proformaPayments = Allocation::find()
		->joinWith(['invoice i' => function($query) use($invoiceModel){
			$query->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
		}])
		->joinWith(['payment p' => function($query) use($invoiceModel){
			$query->where(['p.user_id' => $invoiceModel->user_id]);
		}])
		->all();
		$proformaAmount = 0;
		if(! empty($proformaPayments)){
			foreach($proformaPayments as $proformaPayment){
				$proformaAmount += $proformaPayment['amount'];	
			}
		}
	$invoicePayments = Allocation::find()
		->joinWith(['invoice i' => function($query) use($invoiceModel){
			$query->where(['i.type' => Invoice::TYPE_INVOICE]);
		}])
		->joinWith(['payment p' => function($query) use($invoiceModel){
			$query->where(['p.user_id' => $invoiceModel->user_id]);
		}])
		->all();
		$invoiceAmount = 0;
		if(! empty($invoicePayments)){
			foreach($invoicePayments as $invoicePayment){
				$invoiceAmount += $invoicePayment['amount'];	
			}
		}
		$balance = ($openingBalance + $proformaAmount) - $invoiceAmount;
echo $invoiceModel->user_id . ' Balance: ' . $balance; 
?>
    <?php $form = ActiveForm::begin(); ?>
 	<div class="row">
        <div class="col-xs-4">
    		<?php echo $form->field($model, 'payment_method_id')->dropDownList(
									ArrayHelper::map(PaymentMethod::find()->all(), 'id', 'name'))?>
        </div>
        <div class="col-xs-4">
			<?php $invoiceTotal = $model->isNewRecord ? $invoiceModel->total : null; ?> 
   			<?php echo $form->field($model, 'amount')->textInput(['value' => $invoiceTotal]) ?>
        </div>
	</div>
</div>
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
		<?php if($balance != 0) : ?>
		<?php echo Html::submitButton(Yii::t('backend', 'Apply Credit'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
		<?php endif; ?>
			<?php 
			if(! $model->isNewRecord){
				echo Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	
			}
		?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
