<?php

use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="p-10">
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['customer-payment-preference/modify', 'id' => $userModel->id]),
        'id' => 'modal-form',
    ]); ?>
	
    <div class="row">
        <div class="col-md-4">
            <?php echo $form->field($model, 'dayOfMonth')->textInput([
                    'class' => 'form-control right-align'
                ])->label('Day of Month');
            ?>
        </div>
        <div class="col-md-8">
            <?php echo $form->field($model, 'expiryDate', [
                'inputTemplate' => '<div class="input-group">{input}<span class="input-group-addon" title="Clear field">
                    <span class="glyphicon glyphicon-remove"></span></span></div>'
                ])->widget(DatePicker::classname(),
		[
                   'options' => [
			'value' => Yii::$app->formatter->asDate(new \DateTime()),
                    ],
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'M dd,yyyy'
                    ]
		])->label('Expiry Date');
            ?>
        </div>
        <div class="col-md-12">
            <?php echo $form->field($model, 'paymentMethodId')->dropdownList(
                ArrayHelper::map(PaymentMethod::find()->active()->paymentPreference()->all(), 'id', 'name'),
                ['prompt' => 'Select payment method'])->label('Payment Method');
            ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).off('click', '.glyphicon-remove').on('click', '.glyphicon-remove', function () {
        $('#customerpaymentpreference-expirydate').val('');
    });
</script>