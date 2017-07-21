<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\switchinput\SwitchInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div id="warning-notification" style="display:none;" class="alert-warning alert fade in"></div>


    <?php $form = ActiveForm::begin([
        'id' => 'invoice-discount-form',
        'action' => Url::to(['invoice/discount', 'id' => $model->id]),
    ]); ?>
    <div class="row">
        <div class="col-xs-6">
            <?php echo $form->field($customerDiscount, 'value')->textInput(['id' => 'customer-discount-value', 'name' => 'CustomerDiscount[value]'])->label('Customer Discount'); ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($customerDiscount, 'valueType')->widget(SwitchInput::classname(),
                [
                'options' => [
                    'name' => 'CustomerDiscount[valueType]',
                    'id' => 'customer-discount-type',
                ],
                'pluginOptions' => [
                    'handleWidth' => 50,
                    'onText' => '$',
                    'offText' => '%',
                ],
            ])->label('Customer Discount Type');?>
        </div>
        <div class="col-xs-6">
            <?php echo $form->field($enrolmentDiscount, 'value')->textInput(['id' => 'enrolment-discount-value', 'name' => 'EnrolmentDiscount[value]'])->label('Enrolment Discount'); ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($enrolmentDiscount, 'valueType')->widget(SwitchInput::classname(),
                [
                'options' => [
                    'name' => 'EnrolmentDiscount[valueType]',
                    'id' => 'enrolment-discount-type',
                ],
                'pluginOptions' => [
                    'handleWidth' => 50,
                    'onText' => '$',
                    'offText' => '%',
                ],
            ])->label('Enrolment Discount Type');?>
        </div>
    <div class="form-group col-xs-12">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default discount-cancel']);?>
    </div>
    <?php ActiveForm::end(); ?>
</div>