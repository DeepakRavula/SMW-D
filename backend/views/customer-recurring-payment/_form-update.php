<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\bootstrap\Html;
use common\models\PaymentFrequency;
use common\models\CustomerRecurringPayment;
use Carbon\Carbon;
/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="customer-recurring-payment-form">
    
<div id="index-error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div id="warning-notification-recurring-payment" style="display:none;" class="alert-info alert fade in"></div>
<?php 
        $form = ActiveForm::begin([
        'id' => 'modal-form',
    ]); ?>
    <?php $paymentMethods = PaymentMethod::find()
        ->andWhere(['active'=> PaymentMethod::STATUS_ACTIVE])
        ->andWhere(['displayed' => 1])
        ->orderBy(['sortOrder' => SORT_ASC])
        ->all(); ?>
    <div class="row">
	<div class="col-md-4 ">
            <?php $day = CustomerRecurringPayment::getDaysList();?> 
        <?php $model->startDate = Carbon::parse($model->startDate)->format('M d, Y');   ?>
    	<?= $form->field($model, 'startDate')->widget(DatePicker::className(), [
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                        'changeMonth' => true,
                        'defaultDate' => (new \DateTime($model->startDate))->format('M d, Y'),
                        'yearRange' => '-70:+20',
                        'changeYear' => true,
                        ], ])->textInput(['placeholder' => 'Select Start Date'])->label('As Of');?>
    </div>
    <div class="col-md-4 ">
    	<?= $form->field($model, 'paymentDay')->dropDownList($day, ['prompt'=>'Choose a Day'])->label('On The')?>
    </div>
    <div class="col-md-4 ">
    <?php $frequency= ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name'); ?>
    <?= $form->field($model, 'paymentFrequencyId')->dropDownList($frequency, ['prompt'=>'Choose a Frequency'])->label('Every');?>    
    </div>
    </div>
    <div class="row">
    <div class="col-md-4 ">
    <?= $form->field($model, 'paymentMethodId')->dropDownList(ArrayHelper::map($paymentMethods, 'id', 'name'))
                ->label('Via'); ?>    
    </div>
    <div class="col-md-4 ">
    <?php if (!$model->isNewRecord) {
            $model->expiryDate = (new \DateTime($model->expiryDate))->format('M d, Y'); 
        }?>
    <?= $form->field($model, 'expiryDate')->widget(DatePicker::className(), [
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                        'changeMonth' => true,
                        'yearRange' => '-70:+20',
                        'changeYear' => true,
                        ], ])->textInput(['placeholder' => 'Select Expiry Date'])->label('Until');?>    
    </div>
    <div class="col-md-4 ">
    <?= $form->field($model, 'amount')->textInput(['value' => Yii::$app->formatter->asDecimal($model->amount, 2),
            'class' => 'text-right form-control'])->label('In The Amount Of'); ?>    
    </div>
    </div>
    <div class="row">
    <div class="col-md-4">
    <?= $form->field($model, 'isRecurringPaymentEnabled')->checkbox()->label('Enabled'); ?>
    </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?= Html::label('To Be Applied Towards The Following Enrolments', ['class' => 'admin-login']) ?>
<?= $this->render('_enrolment', [
    'enrolmentDataProvider' => $enrolmentDataProvider,
    'customerRecurringPaymentModel' =>  $model,
]);
?>
<script>
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Recurring Payment</h4>');
        $('#popup-modal .modal-dialog').css({'width': '800px'});
            $('#modal-save').addClass('customer-recurring-payment-modal-update');
            $('#modal-save').removeClass('modal-save');
        $('#warning-notification-recurring-payment').html('SMW will automatically record a payment\n\
                                for this customer at the frequency set below.\n\
                                It will enter the payment on the Entry Day\n\
                                and post-date it for the Payment Day\n\
                                . It will do this for the Amount indicated via the Payment Method\n\
                                    indicated until the Expiry Date is reached.\n\
                                    This recurring payment will begin as of the next ocurrence of the Entry Day.').fadeIn();
    });

    $(document).on('modal-success', function(event, params) {
        $.pjax.reload({container: "#recurring-payment-list", replace: false, timeout: 4000});
        return false;
    });
    
    $(document).on('modal-delete', function(event, params) {
        $.pjax.reload({container: "#recurring-payment-list", replace: false, timeout: 4000});
        return false;
    });
   
    $(document).off('click', '.customer-recurring-payment-modal-update').on('click', '.customer-recurring-payment-modal-update', function(){
        var enrolmentIds = $('#enrolment-index').yiiGridView('getSelectedRows');
        var id = <?= $model->id ?>;
        var params = $.param({'id' : id});
        if (!$.isEmptyObject(enrolmentIds)) {
            params = $.param({'CustomerRecurringPaymentEnrolment[enrolmentIds]': enrolmentIds});
        } 
                    $.ajax({
                        url    : '<?=Url::to(['customer-recurring-payment/update' ])?>?' +params,
                        type   : 'post',
                        dataType: "json",
                        data   : $('#modal-form').serialize(),
                        success: function(response)
                        {
                            if (response.status) {
                                $('#popup-modal').modal('hide');
                                $.pjax.reload({container: "#recurring-payment-list", replace: false, timeout: 4000});
                                }
                            else {
                                if (response.message) {
                                    $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                }
                                if (response.error) {
                                    $('#index-error-notification').text(response.error).fadeIn().delay(5000).fadeOut();
                                }
                            }
                        }
                    });
        return false;
    });

</script> 