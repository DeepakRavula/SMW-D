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
<div class=" p-10">
<?php $form = ActiveForm::begin([
    'id' => 'payment-form',
	'action' => Url::to(['payment/invoice-payment', 'id' => $invoice->id]),
	'enableAjaxValidation' => true,
	'enableClientValidation' => false
]); ?>
   <div class="row">
	   <div class="col-md-5">
            <?php echo $form->field($model, 'payment_method_id')->dropDownList(
 ArrayHelper::map(PaymentMethod::find()
        		->where([
                	'active' => PaymentMethod::STATUS_ACTIVE,
                	'displayed' => 1,
            	])
      			->orderBy(['sortOrder' => SORT_ASC])->all(), 'name', 'name') );
            ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-4 amount">
            <?= $form->field($model, 'amount')->textInput();?>
        </div>
       <?php if ($model->payment_method_id === PaymentMethod::TYPE_CHEQUE) : ?>
           <div class="col-md-3">
               <?= $form->field($model, 'reference')->textInput()->label('Cheque Number'); ?>
           </div>
       <?php elseif ($model->payment_method_id !== PaymentMethod::TYPE_CASH) : ?>
           <div class="col-md-3">
               <?= $form->field($model, 'reference')->textInput(); ?>
           </div>
       <?php endif; ?>
		<div class="col-md-5">
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
        <div class="clearfix"></div>
	   <div class="col-md-6 form-group">
        <?= Html::submitButton(Yii::t('backend', 'Pay'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default payment-cancel-btn']);?>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>
<script>
var paymentMethods = {
	'cash' : 1,
	'cheque' : 2,
}
$(document).ready(function() {
	$(document).on('change', '#payment-payment_method_id', function() {
			
	});	
});	
</script>