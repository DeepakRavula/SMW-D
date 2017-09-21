<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\discount\CustomerDiscount;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="p-l-20">
    <div id="warning-notification" style="display:none;" class="alert-warning alert fade in"></div>
	<h4><strong>Set Discount (%)</strong></h4>
	<?php $form = ActiveForm::begin([
		'id' => 'customer-discount',
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
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
		<?php echo Html::submitButton(Yii::t('backend', 'Cancel'), ['class' => 'btn btn-default customer-discount-cancel', 'name' => 'signup-button']) ?>
		<?php if(! empty($customerDiscount)) : ?>
			<?= Html::a('Delete', [
            'customer-discount/delete', 'id' => $userModel->id
        ],
        [
			'id' => 'customer-discount-delete',
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this payment?',
                'method' => 'post',
            ]
        ]); ?>
			
		<?php endif; ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>