<?php

use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\jui\DatePicker;

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
           <?php echo $form->field($model, 'expiryDate')->widget(DatePicker::className(), [
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                     'defaultDate' => (new \DateTime($model->expiryDate))->format('M d, Y'),
                    'changeMonth' => true,
                    'yearRange' => '-10:+20',
                    'changeYear' => true,
                   
                ],
               
            ])->textInput(['placeholder' => 'Select Date']);
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
