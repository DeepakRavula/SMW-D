<?php

use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Location;
use common\models\User;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php 
    $paymentMethods = PaymentMethod::find()
        ->andWhere(['active'=> PaymentMethod::STATUS_ACTIVE])
        ->andWhere(['displayed' => 1])
        ->orderBy(['sortOrder' => SORT_ASC])
        ->all();  

    $customers = ArrayHelper::map(User::find()
        ->notDeleted()
        ->customersAndGuests(Location::findOne(['slug' => \Yii::$app->location])->id)
        ->all(), 'id', 'publicIdentity');
?>
<div id="index-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="row">
<div class="col-md-6">
<div id="lessonpayment-error-notification" style="display:none;" class="alert-danger alert fade in text-center"></div>
</div>
</div>
<div class="receive-payment-form">

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['payment/receive']),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['payment/validate-receive'])
    ]); ?>

    <div class="row">
        <div class="col-xs-2">
            <?= $form->field($paymentModel, 'user_id')->widget(Select2::classname(), [
                'data' => $customers,
                'options' => [
                    'placeholder' => 'customer',
                    'id' => 'customer-payment'
                ]
            ])->label('Customer'); ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
                'value'  => Yii::$app->formatter->asDate($model->date),
                'dateFormat' => 'php:M d, Y',
                'options' => [
                    'class' => 'form-control',
                    'readOnly' => true,
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'yearRange' => '1500:3000',
                    'changeYear' => true,
                ]
            ])->label('Date'); ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($paymentModel, 'payment_method_id')->dropDownList(ArrayHelper::map($paymentMethods, 'id', 'name'))
                ->label('Payment Method'); ?>
        </div>
	    <div class="col-xs-2">
            <?= $form->field($paymentModel, 'reference')->textInput(['class' => 'form-control'])->label('Reference'); ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($model, 'amount')->textInput(['class' => 'text-right form-control'])->label('Amount Received'); ?>
        </div>
        
    </div>
   
    <?= $form->field($model, 'amountNeeded')->hiddenInput(['id' => 'amount-needed-value'])->label(false); ?>
    <?= $form->field($model, 'selectedCreditValue')->hiddenInput(['id' => 'selected-credit-value'])->label(false); ?>
    <?= $form->field($model, 'amountToDistribute')->hiddenInput()->label(false); ?>
    <div class ="row">
       <div class = "col-md-12">
            <div class ="pull-left">
                <?= $form->field($model, 'notes')->textArea(['class' => 'form-control','rows' => '3','cols' => '80'])->label('Notes'); ?>
            </div>
       </div>             
    </div>
    <?php ActiveForm::end(); ?>
    
    <?= Html::label('Lessons', ['class' => 'admin-login']) ?>
    <?= $this->render('_lesson-line-item', [
        'model' => $model,
        'isCreatePfi' => false,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
        'searchModel' => $searchModel
    ]);
    ?>

    <?= Html::label('Group Lessons', ['class' => 'admin-login']) ?>
    <?= $this->render('_group-lesson-line-item', [
        'model' => $model,
        'isCreatePfi' => false,
        'lessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
        'searchModel' => $groupLessonSearchModel
    ]);
    ?>
<?php if($invoiceLineItemsDataProvider->totalCount > 0) : ?>
    <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
    <?= $this->render('_invoice-line-item', [
        'model' => $model,
        'isCreatePfi' => false,
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        'searchModel' => $searchModel
    ]);
    ?>
<?php endif;?>
<?php if($creditDataProvider->totalCount > 0) : ?>
    <?= Html::label('Credits', ['class' => 'admin-login']) ?>
    <?= $this->render('_credits-available', [
        'creditDataProvider' => $creditDataProvider,
    ]);
    ?>
<?php endif;?>
    
    <div class ="pull-right">
    <dl class = "dl-horizontal">
    <?php if($creditDataProvider->totalCount > 0) : ?>
     <dt class = "pull-left receive-payment-text">Available Credits   :   </dt>
     <dd><span class="pull-right credit-available receive-payment-text-value dollar">0.00</span></dd>
     <dt class = "pull-left receive-payment-text">Selected Credits    :   </dt>
     <dd><span class="pull-right credit-selected receive-payment-text-value dollar">0.00</span></dd>
    <?php endif;?>
    <dt class = "pull-left receive-payment-text">Amount To Apply     :   </dt>
    <dd><span class=" pull-right amount-to-apply receive-payment-text-value dollar">0.00</span></dd>
    <dt class = "pull-left receive-payment-text">Amount To Credit    :   </dt>
    <dd><span class=" pull-right amount-to-credit receive-payment-text-value dollar">0.00</span></dd>
    </dl>
</div>
<?php $prId = $model->prId ?>
<script>
    var lockTextBox = false;
    var receivePayment = {
        setAction: function() {
            var prId = '<?= $prId; ?>';
            var userId = $('#customer-payment').val();
            
            var lessonPayments = new Array();
            var groupLessonPayments = new Array();
            var invoicePayments = new Array();

            var paymentCredits = new Array();
            var invoiceCredits = new Array();

            var canUsePaymentCredits = 0;
            var canUseInvoiceCredits = 0;

            $('.credit-items-value').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    var amount = $(this).find('.credit-amount').val();
                    var creditId = $(this).find('.credit-type').attr('creditId');
                    var creditType = $(this).find('.credit-type').text();
                    if (creditType == 'Invoice Credit') {
                        invoiceCredits.push({ id: creditId, value: amount });
                        canUseInvoiceCredits = 1;
                    } 
                    if (creditType == 'Payment Credit') {
                        paymentCredits.push({ id: creditId, value: amount });
                        canUsePaymentCredits = 1;
                    }
                }
            });
            $('.lesson-line-items').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    var lessonId = $(this).find('.payment-amount').attr('lessonId');
                    var amount = $(this).find('.payment-amount').val();
                    lessonPayments.push({ id: lessonId, value: amount });
                }
            });
            $('.group-lesson-line-items').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    var lessonId = $(this).find('.payment-amount').attr('lessonId');
                    var amount = $(this).find('.payment-amount').val();
                    groupLessonPayments.push({ id: lessonId, value: amount });
                }
            });
            $('.invoice-line-items').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    var invoiceId = $(this).find('.payment-amount').attr('invoiceId');
                    var amount = $(this).find('.payment-amount').val();
                    invoicePayments.push({ id: invoiceId, value: amount });
                }
            });
            var formData = $('#modal-form').serializeArray();
            var paymentDataObject = { 'PaymentForm[lessonPayments]': lessonPayments, 'PaymentForm[groupLessonPayments]': groupLessonPayments, 
                'PaymentForm[invoicePayments]': invoicePayments, 'PaymentForm[paymentCredits]': paymentCredits, 
                'PaymentForm[invoiceCredits]': invoiceCredits, 'PaymentForm[canUsePaymentCredits]': canUsePaymentCredits, 
                'PaymentForm[canUseInvoiceCredits]': canUseInvoiceCredits, 'PaymentForm[userId]' : userId, 'PaymentForm[prId]': prId
            };
            var formDataObj = {};
            var allData = $.each(formData, function( index, value ) {
                var key = value.name;
                formDataObj[key] = value.value;
            });
            var paymentDataObject = $.extend({}, formDataObj, paymentDataObject);
            var data = $.param(paymentDataObject);
            return data;
        },
        calcAmountNeeded : function() {
            var amountToDistribute = parseFloat('0.0');
            $('.line-items-value').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    if ($.isEmptyObject($(this).find('.payment-amount').val())) {
                        var balance = $(this).find('.invoice-value').text();
                        balance = balance.replace('$', '');
                        balance = balance.replace(',', '');
                        balance = balance.match(/\d+\.?\d*/)[0];
                        $(this).find('.payment-amount').val(balance);
                    }
                    var amount = $(this).find('.payment-amount').val();
                    amount = amount.match(/\d+\.?\d*/)[0];
                    amountToDistribute += parseFloat(amount);
                }
            });
            var amountNeeded = parseFloat('0.00');
            $('.line-items-value').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    var balance = $(this).find('.payment-amount').val();
                    balance = balance.match(/\d+\.?\d*/)[0];
                    amountNeeded += parseFloat(balance);
                }
            });
            
            $('.line-items-value').each(function() {
                if (!$(this).find('.check-checkbox').is(":checked")) {
                    $(this).find('.payment-amount').val('');
                }
            });
            $('.credit-items-value').each(function() {
                if (!$(this).find('.check-checkbox').is(":checked")) {
                    $(this).find('.credit-amount').val('');
                }
            });
            $('#paymentform-amounttodistribute').val(amountToDistribute);
            var creditAmount = parseFloat('0.00');
            $('.credit-items-value').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    if ($.isEmptyObject($(this).find('.credit-amount').val())) {
                        var balance = $(this).find('.credit-value').text();
                        balance = balance.replace('$', '');
                        balance = balance.replace(',', '');
                        $(this).find('.credit-amount').val(balance);
                    }
                    var amount = $(this).find('.credit-amount').val();
                    amount = amount.match(/\d+\.?\d*/)[0];
                    creditAmount += parseFloat(amount);
                }
            });
            $('#selected-credit-value').val((creditAmount).toFixed(2));
            $('.credit-selected').text((creditAmount).toFixed(2));
            $('.credit-selected').digits();
            $('.amount-to-apply').text((amountToDistribute).toFixed(2));
            $(".amount-to-apply").digits();
            var amountReceived = $('#paymentform-amount').val();
            if (!lockTextBox) {
                var amountReceived = amountNeeded - creditAmount  < 0 ? amountNeeded > 0 ? '0.00' : amountNeeded - creditAmount : (-(creditAmount - amountNeeded)).toFixed(2);
                $('#paymentform-amount').val(amountReceived);
            }
            var amountToCredit = 0.00;
            amountToCredit = parseFloat(creditAmount) + (amountReceived == '' ? parseFloat('0.00') : parseFloat(amountReceived)) - parseFloat(amountToDistribute);
            amountToCredit = amountToCredit.toString();
            amountToCredit = amountToCredit.match(/\d+\.?\d*/)[0]
            $('.amount-to-credit').text(parseFloat(amountToCredit).toFixed(2));
            $('.amount-to-credit').digits();
            $('#amount-needed-value').val(parseFloat(amountNeeded).toFixed(2));
            $("#amount-needed-value").digits();
            if(amountNeeded > 0) {
                setAmountNeeded = amountNeeded;
            }
            else {
                setAmountNeeded = amountNeeded - creditAmount;
            }
            $('.amount-needed-value').text((setAmountNeeded).toFixed(2));
            $('.amount-needed-value').digits();
        },
        setAvailableCredits : function() {
            var creditAmount = parseFloat('0.00');
            $('.credit-items-value').each(function() {
                var balance = $(this).find('.credit-value').text();
                balance = balance.replace('$', '');
                balance = balance.replace(',', '');
                creditAmount += parseFloat(balance);
            });
            $('.credit-available').text((creditAmount).toFixed(2));
            $('.credit-available').digits();
        }
    };

    $(document).ready(function () {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        var header = '<div class="row"> <div class="col-md-6"> <h4 class="m-0">Receive Payment</h4> </div> <div class="col-md-6"> <h4 class="amount-needed pull-right">Amount Needed <span class="amount-needed-value dollar">0.00</span></h4> </div> </div>'; 
        $('#popup-modal .modal-dialog').css({'width': '1300px'});
        $('#popup-modal').find('.modal-header').html(header);
        $('#modal-save').text('Save');
        $('.modal-cancel').text('Close');
        $('#modal-save').show();
        $('#modal-save').removeClass('modal-save');
        $('#modal-save').addClass('recive-payment-modal-save');
        $('.select-on-check-all').prop('checked', true);
        receivePayment.calcAmountNeeded();
        receivePayment.setAvailableCredits();
    });
    $.fn.digits = function(){ 
    return this.each(function(){ 
        $(this).text( $(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") ); 
    });
}

    $(document).off('click', '.recive-payment-modal-save').on('click', '.recive-payment-modal-save', function () {
        $('#modal-spinner').show();
	    modal.disableButtons();
        var data = receivePayment.setAction();
        var url = '<?= Url::to(["payment/receive"]) ?>';
        $.ajax({
            url: url,
            type: 'post',
            dataType: "json",
            data: data,
            success: function (response)
            {
                $('#modal-spinner').hide();
                if (response.status)
                {
                    modal.restoreButtonSettings();
                    $('#modal-spinner').hide();
                    if (!$.isEmptyObject(response.data)) {
                        $('#modal-content').html(response.data);
                        $(document).trigger("modal-next", response);
                    } else if (!$.isEmptyObject(response.dataUrl)) {
                        modal.renderUrlData(response.dataUrl);
                    } else {
                        $(document).trigger("modal-success", response);
                        $('#popup-modal').modal('hide');            
                    }
                } else {
                    if (response.errors['paymentform-lessonpayments']) {
                        $('#lessonpayment-error-notification').html(response.errors['paymentform-lessonpayments']).fadeIn().delay(5000).fadeOut();
                    }
                    $('#modal-form').yiiActiveForm('updateMessages', response.errors, true);
                    $('.recive-payment-modal-save').attr('disabled', true);
                    $(document).trigger("modal-error", response);
                }
                modal.enableButtons();
            }
        });
        return false;
    });

    $(document).off('change', '#credit-line-item-grid, #invoice-line-item-grid, #lesson-line-item-grid, #group-lesson-line-item-grid, .select-on-check-all, input[name="selection[]"]').on('change', '#credit-line-item-grid, #invoice-line-item-grid, #lesson-line-item-grid, #group-lesson-line-item-grid, .select-on-check-all, input[name="selection[]"]', function () {
        receivePayment.calcAmountNeeded();
        return false;
    });

    $(document).off('change', '.payment-amount, .credit-amount').on('change', '.payment-amount, .credit-amount', function () {
        var payment = $(this).val();
        var id = $(this).attr('id');
        if (!$.isEmptyObject(payment)) {
            var balance = $(this).closest('td').prev('td').text();
            balance = balance.replace('$', '');
            balance = balance.replace(',', '');
            id = id.replace('#', '');
            if ($.isNumeric(payment)) {
                if (parseFloat(payment) > parseFloat(balance)) {
                    $('.field-'+id).addClass('has-error');
                    $('.field-'+id).find('.help-block').html("<div style='color:#dd4b39'>Can't over pay!</div>");
                    $('.recive-payment-modal-save').attr('disabled', true);
                } else {
                    $('.recive-payment-modal-save').attr('disabled', false);
                    $('.field-'+id).removeClass('has-error');
                    $('.field-'+id).find('.help-block').html("");
                }
            } else {
                $('.field-'+id).addClass('has-error');
                $('.field-'+id).find('.help-block').html("<div style='color:#dd4b39'>Amount must be a number!</div>");
                $('.recive-payment-modal-save').attr('disabled', true);
            }
        }
        
        receivePayment.calcAmountNeeded();
        return false;
    });

    $(document).off('keyup', '#paymentform-amount').on('keyup', '#paymentform-amount', function () {
        lockTextBox = true;
        receivePayment.calcAmountNeeded();
        receivePayment.setAvailableCredits();
        return false;
    });

    $(document).off('change', '#customer-payment').on('change', '#customer-payment', function () {
        $('#modal-spinner').show();
        var userId = $('#customer-payment').val();
        var params = $.param({ 'PaymentFormLessonSearch[userId]' : userId, 'PaymentFormGroupLessonSearch[userId]': userId });
        var url = '<?= Url::to(['payment/receive']) ?>?' + params;
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

    $(document).off('pjax:success', '#lesson-line-item-listing, #group-lesson-line-item-listing').on('pjax:success', '#lesson-line-item-listing, #group-lesson-line-item-listing', function () {
        receivePayment.calcAmountNeeded();
        return false;
    });

    $(document).off('click', '.modal-back').on('click', '.modal-back', function() {
        var invoiceIds = null;
        $('#modal-spinner').show();
        var userId = $('#customer-payment').val();   
        var params = $.param({ 'id' : userId});     
            $('.modal-back').attr('disabled', true);
            $.ajax({
                url    : '<?= Url::to(['email/customer-statement']) ?>?' + params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        $('#modal-spinner').hide();
                        $('#modal-content').html(response.data);                       
                          
                    }
                }
            });
        return false;
    });

    $(document).off('click', '.print-statement').on('click', '.print-statement', function() {
        $('#popup-modal').modal('hide');
        var userId = $('#customer-payment').val(); 
        var params = $.param({ 'id' : userId});
        var url = '<?= Url::to(['print/customer-statement']) ?>?' + params;
        window.open(url, '_blank');
        return false;
    });
    $(document).off('click', '.modal-print').on('click', '.modal-print', function() {
        var invoiceIds = null;
        $('#modal-spinner').show();
        var userId = $('#customer-payment').val();
        var lessonIds = $('#lesson-line-item-grid').yiiGridView('getSelectedRows');
        if ($('#invoice-line-item-grid').length > 0) {
            var invoiceIds = $('#invoice-line-item-grid').yiiGridView('getSelectedRows');
        }
        var groupLessonIds = $('#group-lesson-line-item-grid').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds) && $.isEmptyObject(invoiceIds) && $.isEmptyObject(groupLessonIds)) {
            $('#modal-spinner').hide();
            $('#index-error-notification').html("Choose any lessons or invoices to create PFI").fadeIn().delay(5000).fadeOut();
        } else {
            
            $('.modal-back').attr('disabled', true);
            $('.modal-save-replaced').attr('disabled', true);
            var params = $.param({ 'PaymentFormLessonSearch[lessonIds]': lessonIds, 'PaymentFormLessonSearch[userId]': userId, 
                'ProformaInvoice[invoiceIds]': invoiceIds, 'PaymentFormGroupLessonSearch[lessonIds]': groupLessonIds });
            $.ajax({
                url    : '<?= Url::to(['proforma-invoice/create']) ?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        window.location.href = response.url;
                    }
                }
            });
        }
        return false;
    });

  </script>
