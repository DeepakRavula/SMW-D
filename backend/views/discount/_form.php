<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\PaymentFrequencyDiscount;
use common\models\FamilyDiscount;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentFrequencyDiscount */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="calendar-event-color-form">
	<div class="p-10">
    <?php $form = ActiveForm::begin(); ?>
	<div class="form-group col-lg-4">
		<strong>Payment Frequency</strong>
	</div>
	<div class="form-group col-lg-4">
		<strong>Individual Discount (%)</strong>
	</div>
	<div class="form-group col-lg-4">
		<strong>Family Discount (%)</strong>
	</div>
	<?php foreach ($paymentFrequencies as $index => $paymentFrequency): ?>
	<?php
		$paymentFrequencyDiscount = PaymentFrequencyDiscount::findOne(['paymentFrequencyId' => $paymentFrequency->id]);
		$familyDiscount = FamilyDiscount::findOne(['paymentFrequencyId' => $paymentFrequency->id]);
		// necessary for update action.
		if (!$paymentFrequency->isNewRecord) {
			echo Html::activeHiddenInput($paymentFrequency, "[{$index}]id");
		}
	?>
	<div class="form-group col-lg-4">
		<?php echo $form->field($paymentFrequency, "[{$index}]name")->textInput(['readonly' => true])->label(false); ?>
	</div>
	<div class="form-group col-lg-4">
		<?php echo $form->field($paymentFrequency, "[{$index}]individualDiscountValue")->textInput(['value' => $paymentFrequencyDiscount->value])->label(false); ?>
	</div>
	<div class="form-group col-lg-4">
    	<?php echo $form->field($paymentFrequency, "[{$index}]familyDiscountValue")->textInput(['value' => $familyDiscount->value])->label(false);?>
	</div>
	<?php endforeach; ?>	
	<div class="form-group col-md-12 p-l-20">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>

</div>