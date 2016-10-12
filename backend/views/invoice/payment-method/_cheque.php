<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>


<div class="payments-form p-l-20 form-well-smw form-well m-t-10 m-b-0">
  <h4  class="m-t-0 m-b-20">Cheque payment</h4>
    <?php $form = ActiveForm::begin([
		'action' => Url::to(['payment/invoice-payment', 'id' => $invoice->id])
	]); ?>
 	<div class="row">
		<div class="col-xs-3">
   			<?php echo $form->field($model, 'amount')->textInput([
				'value' => $amount,
				'placeholder' => 'Amount'
			])->label(false); ?>
        </div>
		<div class="col-xs-3">
   			<?php echo $form->field($model, 'reference')->textInput(['placeholder' => 'Cheque Number'])->label(false); ?>
        </div>
		<div class="col-xs-3">
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
					'autoclose' => true
				]
			])->label(false); ?>
        </div>
	</div>
	<div class="row">
		<?php echo $form->field($model, 'payment_method_id')->hiddenInput(['class' => 'payment-method-id'])->label(false); ?>
	</div>
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
