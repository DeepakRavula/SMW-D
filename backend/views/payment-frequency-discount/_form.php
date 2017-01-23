<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentFrequencyDiscount */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="calendar-event-color-form">
	<div class="p-10">
    <?php $form = ActiveForm::begin(); ?>
	<div class="form-group col-lg-6">
		<strong>Payment Frequency</strong>
	</div>
	<div class="form-group col-lg-6">
		<strong>Set Discount ($)</strong>
	</div>
	<?php foreach ($paymentFrequencies as $index => $paymentFrequency): ?>
	<?php
		// necessary for update action.
		if (!$paymentFrequency->isNewRecord) {
			echo Html::activeHiddenInput($paymentFrequency, "[{$index}]id");
		}
	?>
	<div class="form-group col-lg-6">
	<?php echo $form->field($paymentFrequency, "[{$index}]paymentFrequencyId")->textInput(['readonly' => true, 'value' => $paymentFrequency->paymentFrequency->name])->label(false); ?>
	</div>
	<div class="form-group col-lg-6">
    <?php echo $form->field($paymentFrequency, "[{$index}]value")->textInput()->label(false);
	?>
	</div>
	<?php endforeach; ?>
	<div class="form-group col-md-12 p-l-20">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>

</div>