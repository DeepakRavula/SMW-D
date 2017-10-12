<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\PaymentFrequency;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div id="warning-notification" style="display:none;" class="alert-warning alert fade in"></div>
<div id="enrolment-enddate" style="display:none;" class="alert-danger alert fade in"></div>
    <?php $form = ActiveForm::begin([
        'id' => 'enrolment-update-form',
        'action' => Url::to(['enrolment/edit', 'id' => $model->id]),
    ]); ?>
    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'paymentFrequencyId')->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name'),
				'pluginOptions' => [
                                    'allowClear' => true,
                                ]
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($paymentFrequencyDiscount, 'discount')->textInput()
                    ->label('Payment Frequency Discount'); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($multipleEnrolmentDiscount, 'discount')->textInput()
                            ->label('Multiple Enrolment Discount'); ?>
        </div>
		<div class="clearfix"></div>
		 <div id="spinner" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>
	<div class="form-group col-xs-12">
            <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button', 'id' => 'enrolment-edit-save-btn']) ?>
            <?= Html::a('Cancel', '', ['class' => 'btn btn-default enrolment-edit-cancel']);?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
