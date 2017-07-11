<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\switchinput\SwitchInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'id' => 'invoice-discount-form',
        'action' => Url::to(['invoice/discount', 'id' => $model->id]),
    ]); ?>
    <div class="row">
        <div class="col-xs-4">
            <?php echo $form->field($customerDiscount, 'value')->textInput()->label('Customer Discount'); ?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($customerDiscount, 'valueType')->widget(SwitchInput::classname(),
                [
                'name' => 'valueType',
                'pluginOptions' => [
                    'handleWidth' => 50,
                    'onText' => '$',
                    'offText' => '%',
                ],
            ])->label('Customer Discount Type');?>
        </div>
    <div class="form-group col-xs-12">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default discount-cancel']);?>
    </div>
    <?php ActiveForm::end(); ?>
</div>