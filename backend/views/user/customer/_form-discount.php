<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\CustomerDiscount;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="p-10">
	<h4><strong>Set Discount ($)</strong></h4>
	<?php $form = ActiveForm::begin([
        'action' => Url::to(['customer-discount/create', 'id' => $userModel->id]),
    ]); ?>
	<?php
	$customerDiscount = CustomerDiscount::findOne(['customerId' => $userModel->id]);
	$discount = !empty($customerDiscount) ? $customerDiscount->value : null; ?>
	<div class="row">
        <div class="col-xs-4">
			<?php echo $form->field($model, 'value')->textInput(['placeholder' => 'Discount', 'value' => $discount])->label(false); ?>
        </div>
	</div>
	<div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>