<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\bootstrap\Html;
/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="blog-form">
<?php 
        $url = Url::to(['blog/update', 'id' => $model->id]);
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
        <?php $day= ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', 
        '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', 
        '17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22', '23' => '23', '24' => '24', 
        '25' => '25', '26' => '26', '27' => '27', '28' => '28']; ?>
    	<?= $form->field($model, 'entryDay')->dropDownList($day, ['prompt'=>'Choose a Day'])?>
    </div>
    <div class="col-md-4 ">
    	<?= $form->field($model, 'paymentDay')->dropDownList($day, ['prompt'=>'Choose a Day'])?>
    </div>
    <div class="col-md-4 ">
    <?php $frequency= ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', 
        '9' => '9', '10' => '10', '11' => '11', '12' => '12']; ?>
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
        $('#modal-save').addClass('customer-recurring-payment-modal-save');
        $('#modal-save').removeClass('modal-save');
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
        var id = <?= $id ?>;
        var params = $.param({'id' : id,'CustomerRecurringPaymentEnrolment[enrolmentIds]': enrolmentIds});
                    $.ajax({
                        url    : '<?=Url::to(['customer-recurring-payment/create' ])?>?' +params,
                        type   : 'post',
                        dataType: "json",
                        data   : $('#modal-form').serialize(),
                        success: function(response)
                        {
                            if (response.status) {
                                 
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