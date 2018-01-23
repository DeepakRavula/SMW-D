<?php

use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="p-10">
	<?php $form = ActiveForm::begin([
        'action' => Url::to(['customer-payment-preference/modify', 'id' => $userModel->id]),
        'id' => 'payment-preference-form',
    ]); ?>
	
    <div class="row">
			<?php echo $form->field($model, 'dayOfMonth')->textInput(['class' => 'form-control right-align'])->label('Day of Month'); ?>
			<?php echo $form->field($model, 'paymentMethodId')->dropdownList(ArrayHelper::map(PaymentMethod::find()->active()->paymentPreference()->all(), 'id', 'name'), ['prompt' => 'Select payment method'])->label('Payment Method'); ?>
	</div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::a('Cancel', null, ['id' => 'cancel', 'class' => 'btn btn-default']); ?>
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
            </div>
<div class="pull-left">
        <?php if (!empty($model->id)) : ?>
            <?= Html::a(
        'Delete',
        null,
                [
                    'class' => 'btn btn-danger payment-preference-delete',
                    'preferenceId' => $model->id,
                ]
    ); ?>
        <?php endif; ?>
	</div>
        </div>
    </div>
	<?php ActiveForm::end(); ?>
</div>
</div>