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
use common\models\User;
use common\models\Location;
use kartik\select2\Select2;
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
        ->all(); 
        $customers = ArrayHelper::map(User::find()
            ->notDeleted()
            ->customersAndGuests(Location::findOne(['slug' => \Yii::$app->location])->id)
            ->all(), 'id', 'publicIdentity');?>
    <?php $model->amount = CustomerRecurringPayment::DEFAULT_RATE;?>       
    <?php  $disabled = false;
    if (!$model->customerId) {
            $disabled = true;
        }?>        
    <div class="row">
	<div class="col-md-4 ">
            <?php $day = CustomerRecurringPayment::getDaysList();?>
            <?php $month = CustomerRecurringPayment::getMonthsList();?>
            <?php $year = CustomerRecurringPayment::getYearsList();?>
            <?php $model->startDate = Carbon::now()->format('M d, Y');   ?>
        <?= $form->field($model, 'customerId')->widget(Select2::classname(), [
            'data' => $customers,
            'options' => [
                'placeholder' => 'customer',
                'id' => 'customer-recurring-payment',
            ]
        ])->label('Customer'); ?>
    	<?= $form->field($model, 'startDate')->widget(DatePicker::className(), [
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                        'changeMonth' => true,
                        'yearRange' => '-70:+20',
                        'defaultDate' => (new \DateTime())->format('M d, Y'),
                        'changeYear' => true,
                        'firstDay' => 1,
                        'disabled' => $disabled,
                        ], ])->textInput(['placeholder' => 'Select Start Date'])->label('As Of');?>
    </div>
    <div class="col-md-4 ">
    	<?= $form->field($model, 'paymentDay')->dropDownList($day, ['disabled' => $disabled,])->label('On The');?>
    </div>
    <div class="col-md-4 ">
    <?php $frequency= ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name'); ?>
    <?= $form->field($model, 'paymentFrequencyId')->dropDownList($frequency, ['disabled' => $disabled,])->label('Every');?>    
    </div>
    </div>
    <div class="row">
    <div class="col-md-4 ">
    <?= $form->field($model, 'paymentMethodId')->dropDownList(ArrayHelper::map($paymentMethods, 'id', 'name'), ['disabled' => $disabled,])->label('Via'); ?>    
    </div>
   <div class="col-md-2 ">
    <?= $form->field($model, 'expiryMonth')->dropDownList($month,  ['style' => 'width:70px !important','prompt' => 'MM'])->label('Untill');?>
    </div>
    <div class="col-md-2 ">
    <?= $form->field($model, 'expiryYear')->dropDownList($year,  ['style' => 'width:80px !important; margin-left:-40px; margin-top:5px','prompt' => 'YEAR'])->label(' ');?>
    </div>
    <div class="col-md-4 ">
    <?= $form->field($model, 'amount')->textInput(['value' => Yii::$app->formatter->asDecimal($model->amount, 2),
            'class' => 'text-right form-control', 'disabled' => $disabled])->label('In The Amount Of'); ?>    
    </div>
    </div>
    <div class="row">
    <div class="col-md-4">
    <?php if ($model->isNewRecord) {
            $model->isRecurringPaymentEnabled = true;
        } ?>
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
        $('#modal-save').show();
        $('#modal-save').addClass('customer-recurring-payment-modal-save');
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

    $(document).off('change', '#customer-recurring-payment').on('change', '#customer-recurring-payment', function () {
        $('#modal-spinner').show();
        var userId = $('#customer-recurring-payment').val();
        var params = $.param({ 'CustomerRecurringPayment[customerId]' : userId});
        var url = '<?= Url::to(['customer-recurring-payment/create']) ?>?' + params;
        $.ajax({
            url    : url,
            type   : 'get',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#modal-spinner').hide();
                }
            }
        });
        return false;
    });

    $(document).off('change', '.select-on-check-all, input[name="selection[]"]').on('change', '.select-on-check-all, input[name="selection[]"]', function () {
        recurringPayment.calcDueAmount();
    });

    $(document).off('click', '.customer-recurring-payment-modal-save').on('click', '.customer-recurring-payment-modal-save', function(){
        var enrolmentIds = $('#enrolment-index').yiiGridView('getSelectedRows');
        var id = '<?= $model->customerId ?>';
        var params = $.param({'id' : id});
        if (!$.isEmptyObject(enrolmentIds)) {
            params = $.param({'id' : id, 'CustomerRecurringPaymentEnrolment[enrolmentIds]': enrolmentIds});
        } 
              $.ajax({
                        url    : '<?=Url::to(['customer-recurring-payment/create' ])?>?' +params,
                        type   : 'post',
                        dataType: "json",
                        data   : $('#modal-form').serialize(),
                        success: function(response)
                        {
                            if (response.status) {
                                $('#popup-modal').modal('hide');
                                $.pjax.reload({container: "#recurring-payment-listing", replace: false, timeout: 4000,async:false});
                                $.pjax.reload({container: "#user-log", replace: false, timeout: 4000});
                                }
                            else {
                                if (response.message) {
                                    $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                }
                                if (response.error) {
                                    $('#index-error-notification').text(response.error).fadeIn().delay(5000).fadeOut();
                                }
                                if (response.errors) {
                                $('#modal-form').yiiActiveForm('updateMessages', response.errors, true);
                                }
                            }
                        }
                    });
        return false;
    });

    var recurringPayment = {      
        calcDueAmount : function() {
            var dueAmount = parseFloat('0.00');
            var totalAmount = 0.00;
            $('.enrolment-items').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                dueAmount = $(this).find('.check-checkbox').attr('dueAmount');
                totalAmount += parseFloat(dueAmount);
                }
            });
            $('#customerrecurringpayment-amount').val((totalAmount).toFixed(2));
        }
    };
 </script> 