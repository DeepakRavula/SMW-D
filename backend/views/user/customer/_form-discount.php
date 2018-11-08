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
	<?php $form = ActiveForm::begin([
        'id' => 'customer-discount',
        'action' => Url::to(['customer-discount/create', 'id' => $userModel->id]),
    ]); ?>
	<?php
    $customerDiscount = CustomerDiscount::findOne(['customerId' => $userModel->id]);
    $discount = !empty($customerDiscount) ? $customerDiscount->value : null; ?>
	<div class="row">
        <div class="col-xs-4">
			<?php echo $form->field($model, 'value', [
    'inputTemplate' => '<div class="input-group">'
    . '{input}<span class="input-group-addon">%</span></div>'])->textInput(['class' => 'right-align form-control', 'value' => $discount])->label('Discount'); ?>
        </div>
	</div>
    <div class="row">
        <div class="col-md-12">
	<div class="pull-right">
        <?php echo Html::submitButton(Yii::t('backend', 'Cancel'), ['class' => 'btn btn-default customer-discount-cancel', 'name' => 'signup-button']) ?>
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
    </div>
        </div></div>
	<?php ActiveForm::end(); ?>
</div>