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
/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="customer-recurring-payment-form">
<?php 
    $url = Url::to(['customer-recurring-payment/update', 'id' => $model->id]);
    if ($model->isNewRecord) {
        $url = Url::to(['customer-recurring-payment-enrolment/create', 'id' => $id]);
    }
        $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
    ]); ?>
    <?php $paymentMethods = PaymentMethod::find()
        ->andWhere(['active'=> PaymentMethod::STATUS_ACTIVE])
        ->andWhere(['displayed' => 1])
        ->orderBy(['sortOrder' => SORT_ASC])
        ->all(); ?>
    <div class="row">
	<div class="col-md-4 ">
            <?php $day = CustomerRecurringPayment::getDaysList();?>
    	<?= $form->field($model, 'entryDay')->dropDownList($day, ['prompt'=>'Choose a Day'])?>
    </div>
    <div class="col-md-4 ">
    	<?= $form->field($model, 'paymentDay')->dropDownList($day, ['prompt'=>'Choose a Day'])?>
    </div>
    <div class="col-md-4 ">
    <?php $frequency= ArrayHelper::getColumn(PaymentFrequency::find()->all(), 'id'); ?>
    <?= $form->field($model, 'paymentFrequencyId')->dropDownList($frequency, ['prompt'=>'Choose a Frequency'])?>    
    </div>
    <div class="col-md-4 ">
    <?= $form->field($model, 'paymentMethodId')->dropDownList(ArrayHelper::map($paymentMethods, 'id', 'name'))
                ->label('Payment Method'); ?>    
    </div>
    <div class="col-md-4 ">
    <?= $form->field($model, 'expiryDate')->widget(DatePicker::className(), [
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => '-70:+20',
                'changeYear' => true,
                ], ])->textInput(['placeholder' => 'Select Expiry Date']);?>    
    </div>
    <div class="col-md-4 ">
    <?= $form->field($model, 'amount')->textInput(); ?>    
    </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?= Html::label('Enrolment', ['class' => 'admin-login']) ?>
<?= $this->render('_enrolment', [
    'enrolmentDataProvider' => $enrolmentDataProvider
]);
?>
<script>
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Recurring Payment</h4>');
        $('#popup-modal .modal-dialog').css({'width': '800px'});
        var isNewRecord = '<?= $model->isNewRecord; ?>';
        if (isNewRecord) {
            $('#modal-save').addClass('customer-recurring-payment-modal-save');
            $('#modal-save').removeClass('modal-save');
        }
    });

    $(document).on('modal-success', function(event, params) {
        $.pjax.reload({container: "#recurring-payment-list", replace: false, timeout: 4000});
        return false;
    });
    
    $(document).on('modal-delete', function(event, params) {
        $.pjax.reload({container: "#recurring-payment-list", replace: false, timeout: 4000});
        return false;
    });
   
    $(document).off('click', '.customer-recurring-payment-modal-save').on('click', '.customer-recurring-payment-modal-save', function(){
        var enrolmentIds = $('#enrolment-index').yiiGridView('getSelectedRows');
        var id = <?= $model->customerId ?>;
        var params = $.param({'id' : id,'CustomerRecurringPaymentEnrolment[enrolmentIds]': enrolmentIds});
                    $.ajax({
                        url    : '<?=Url::to(['customer-recurring-payment/create' ])?>?' +params,
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