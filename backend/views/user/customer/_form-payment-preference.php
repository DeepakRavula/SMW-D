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
        <div class="col-xs-4">
			<?php echo $form->field($model, 'dayOfMonth')->textInput()->label('Day of Month'); ?>
        </div>
        <div class="col-xs-8">
			<?php echo $form->field($model, 'paymentMethodId')->dropdownList
                (ArrayHelper::map(PaymentMethod::find()->where(['name' => 'Credit Card'])->all(), 'id', 'name'), ['prompt' => 'Select payment method'])->label('Payment Method'); ?>
        </div>
	</div>
	<div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
        <?= Html::a('Cancel', null, ['id' => 'cancel', 'class' => 'btn btn-primary']); ?>
        <?php if (!empty($model->id)) : ?>
            <?= Html::a('Delete', [
                    'customer-payment-preference/delete', 'id' => $model->id
                ],
                [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this?',
                        'method' => 'post',
                    ]
                ]); ?>
        <?php endif; ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
</div>