<?php

use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="row user-create-form">
<?php $form = ActiveForm::begin([
    'id' => 'payment-form',
	'action' => Url::to(['payment/invoice-payment', 'id' => $invoice->id]),
]); ?>
    <?php echo $form->field($model, 'payment_method_id')->dropDownList(
 ArrayHelper::map(PaymentMethod::find()
        		->where([
                	'active' => PaymentMethod::STATUS_ACTIVE,
                	'displayed' => 1,
            	])
      			->orderBy(['sortOrder' => SORT_ASC])->all(), 'id', 'name') );
            ?>
            <?= $form->field($model, 'amount')->textInput(['class' => 'right-align payment-amount form-control']);?>
	   <div class="reference">
       <?php if ($model->payment_method_id === PaymentMethod::TYPE_CHEQUE) : ?>
               <?= $form->field($model, 'reference')->textInput()->label('Cheque Number'); ?>
       <?php elseif ($model->payment_method_id !== PaymentMethod::TYPE_CASH) : ?>
               <?= $form->field($model, 'reference')->textInput(); ?>
       <?php endif; ?>
		  </div>
	   	<div class="cheque-date">
   			<?php
            $currentDate = (new \DateTime())->format('d-m-Y');
            echo $form->field($model, 'date')->widget(DatePicker::classname(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'options' => [
                    'value' => $currentDate,
                ],
                'pluginOptions' => [
                    'format' => 'dd-mm-yyyy',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ])->label('Cheque Date'); ?>
   </div>   
    <div class="row">
	   <div class="form-group pull-right">
           <?= Html::a('Cancel', '', ['class' => 'btn btn-default payment-cancel-btn']);?>
        <?= Html::submitButton(Yii::t('backend', 'Pay'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
</div>
    <script>
var paymentMethods = {
	'cash' : '4',
	'cheque' : '5'
};
$(document).ready(function() {
	$('.amount').show();
	$('.reference').hide();
	$('.cheque-date').hide();
	$(document).on('change', '#payment-payment_method_id', function() {
		var paymentMethod = $('#payment-payment_method_id').val();
		if(paymentMethod == paymentMethods.cash) {
			$('.amount').show();
			$('.reference').hide();
			$('.cheque-date').hide();	
		}else if(paymentMethod == paymentMethods.cheque) {
			$('.amount').show();
			$('.reference').show();
			$('.cheque-date').show();	
		} else {
			$('.amount').show();
			$('.reference').show();
			$('.cheque-date').hide();	
		}
	});	
});	
</script>