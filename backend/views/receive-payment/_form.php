<?php

use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\jui\Accordion;
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
        ->active()
        ->customers(Location::findOne(['slug' => \Yii::$app->location])->id)
        ->all(), 'id', 'publicIdentity');
?>
<div id="index-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="receive-payment-form">

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['payment/receive']),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['payment/validate-receive'])
    ]); ?>

    <div class="row">
        <div class="col-xs-4">
            <?= $form->field($model, 'userId')->widget(Select2::classname(), [
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
                    'class' => 'form-control'
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'yearRange' => '1500:3000',
                    'changeYear' => true
                ]
            ])->label('Date'); ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($model, 'payment_method_id')->dropDownList(ArrayHelper::map($paymentMethods, 'id', 'name'))
                ->label('Payment Method'); ?>
        </div>
	<div class="col-xs-2">
            <?= $form->field($model, 'reference')->textInput(['class' => 'text-right form-control'])->label('Reference'); ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($model, 'amount')->textInput(['class' => 'text-right form-control'])->label('Amount Received'); ?>
        </div>
    </div>

    <?= $form->field($model, 'amountNeeded')->hiddenInput(['id' => 'amount-needed-value'])->label(false); ?>
    <?= $form->field($model, 'selectedCreditValue')->hiddenInput(['id' => 'selected-credit-value'])->label(false); ?>
    <?= $form->field($model, 'amountToDistribute')->hiddenInput([])->label(false); ?>

    <?php ActiveForm::end(); ?>

    <?= Html::label('Lessons', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_lesson-line-item', [
        'model' => $model,
        'isCreatePfi' => false,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
        'searchModel' => $searchModel
    ]);
    ?>

    <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_invoice-line-item', [
        'model' => $model,
        'isCreatePfi' => false,
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        'searchModel' => $searchModel
    ]);
    ?>

    <?= Html::label('Credits', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_credits-available', [
        'creditDataProvider' => $creditDataProvider,
    ]);
    ?>

    <div class="pull-right">
        <div>
            <h4 class="pull-right amount-needed">Available Credits $<span class="credit-available">0.00</span></h4>
        </div>
        <div>
            <h4 class="pull-right">Selected Credits $<span class="credit-selected">0.00</span></h4>
        </div>
        <div>
            <h4 class="pull-right">Amount To Apply $<span class="amount-to-apply">0.00</span></h4>
        </div>
        <div>
            <h4 class="pull-right">Amount To Credit $<span class="amount-to-credit">0.00</span></h4>
        </div>
    </div>
</div>

<script>
    var receivePayment = {
        setAction: function() {
            var userId = $('#customer-payment').val();
            var lessonIds = $('#lesson-line-item-grid').yiiGridView('getSelectedRows');
            var invoiceIds = $('#invoice-line-item-grid').yiiGridView('getSelectedRows');
            var creditIds = new Array();
            var lessonPayments = new Array();
            var invoicePayments = new Array();
            var creditPayments = new Array();
            var canUsePaymentCredits = 0;
            var canUseInvoiceCredits = 0;
            $('.credit-items-value').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    var creditId = $(this).find('.credit-type').attr('creditId');
                    creditIds.push(creditId);
                    var creditType = $(this).find('.credit-type').text();
                    if (creditType == 'Invoice Credit') {
                        canUseInvoiceCredits = 1;
                    } 
                    if (creditType == 'Payment Credit') {
                        canUsePaymentCredits = 1;
                    }
                }
            });
            $('.lesson-line-items').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    lessonPayments.push($(this).find('.payment-amount').val());
                }
            });
            $('.invoice-line-items').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    invoicePayments.push($(this).find('.payment-amount').val());
                }
            });
            $('.credit-line-items').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    creditPayments.push($(this).find('.credit-amount').val());
                }
            });
            $('.invoice-line-items').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    invoicePayments.push($(this).find('.payment-amount').val());
                }
            });
            var params = $.param({ 'PaymentFormLessonSearch[userId]' : userId, 'PaymentFormLessonSearch[lessonIds]': lessonIds, 
                'PaymentForm[invoiceIds]': invoiceIds, 'PaymentForm[canUsePaymentCredits]': canUsePaymentCredits, 
                'PaymentForm[canUseInvoiceCredits]': canUseInvoiceCredits, 'PaymentForm[creditIds]': creditIds,
                'PaymentForm[lessonPayments]': lessonPayments, 'PaymentForm[invoicePayments]': invoicePayments,
                'PaymentForm[creditPayments]': creditPayments });
            var url = '<?= Url::to(['payment/receive']) ?>?' + params;
            $('#modal-form').attr('action', url);
            return false;
        },
        calcAmountNeeded : function() {
            var amountNeeded = parseFloat('0.00');
            $('.line-items-value').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    var balance = $(this).find('.invoice-value').text();
                    balance = balance.replace('$', '');
                    amountNeeded = parseFloat(amountNeeded) + parseFloat(balance);
                }
            });
            var amountToDistribute = 0.0;
            $('.line-items-value').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")) {
                    if ($.isEmptyObject($(this).find('.payment-amount').val())) {
                        var balance = $(this).find('.invoice-value').text();
                        balance = balance.replace('$', '');
                        $(this).find('.payment-amount').val(balance);
                    }
                    amountToDistribute += parseFloat($(this).find('.payment-amount').val());
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
                        var balance = $(this).find('.credit-value').text();debugger
                        balance = balance.replace('$', '');
                        $(this).find('.credit-amount').val(balance);
                    }
                    creditAmount += parseFloat($(this).find('.credit-amount').val());
                }
            });
            $('#selected-credit-value').val((creditAmount).toFixed(2));
            $('.credit-selected').text((creditAmount).toFixed(2));
            $('.amount-to-apply').text((amountToDistribute).toFixed(2));
            var amountReceived = $('#paymentform-amount').val();
            $('.amount-to-credit').text(((creditAmount + amountReceived) - amountToDistribute).toFixed(2));
            $('#amount-needed-value').val((amountNeeded).toFixed(2));
            $('.amount-needed-value').text((amountNeeded).toFixed(2));
            return false;
        },
        setAvailableCredits : function() {
            var creditAmount = parseFloat('0.00');
            $('.credit-items-value').each(function() {
                var balance = $(this).find('.credit-value').text();
                balance = balance.replace('$', '');
                creditAmount = parseFloat(creditAmount) + parseFloat(balance);
            });
            $('.credit-available').text((creditAmount).toFixed(2));
            return false;
        },
        validateAmount : function() {
            var amountReceived = $('#paymentform-amount').val();
            $('#paymentform-amount').val(amountReceived).trigger('change');
            $('#paymentform-amount').focus();
            $('#paymentform-amount').blur();
        }
    };

    $(document).ready(function () {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        var header = '<div class="row"> <div class="col-md-6"> <h4 class="m-0">Receive Payment</h4> </div> <div class="col-md-6"> <h4 class="amount-needed pull-right">Amount Needed $<span class="amount-needed-value">0.00</span></h4> </div> </div>'; 
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#popup-modal').find('.modal-header').html(header);
        $('.modal-save').text('Save');
        $('.modal-back').text('Create PFI');
        $('.modal-back').removeClass('btn-info');
        $('.modal-back').addClass('btn-default');
        $('.modal-back').show();
        $('.select-on-check-all').prop('checked', true);
        receivePayment.setAction();
        receivePayment.calcAmountNeeded();
        receivePayment.setAvailableCredits();
    });

    $(document).off('change', '#credit-line-item-grid, .payment-amount, .credit-amount, #invoice-line-item-grid, #lesson-line-item-grid .select-on-check-all, input[name="selection[]"]').on('change', '.payment-amount, #credit-line-item-grid, #invoice-line-item-grid, .credit-amount, #lesson-line-item-grid .select-on-check-all, input[name="selection[]"]', function () {
        receivePayment.setAction();
        receivePayment.calcAmountNeeded();
        receivePayment.validateAmount();
        return false;
    });

    $(document).off('change', '#paymentform-amount').on('change', '#paymentform-amount', function () {
        receivePayment.calcAmountNeeded();
        return false;
    });

    $(document).off('change', '#customer-payment').on('change', '#customer-payment', function () {
        $('#modal-spinner').show();
        var userId = $('#customer-payment').val();
        var params = $.param({ 'PaymentFormLessonSearch[userId]' : userId });
        var url = '<?= Url::to(['payment/receive']) ?>?' + params;
        $.pjax.reload({url: url, container: "#invoice-lineitem-listing", replace: false, async: false, timeout: 6000});
        $.pjax.reload({url: url, container: "#lesson-line-item-listing", replace: false, async: false, timeout: 6000});
        $.pjax.reload({url: url, container: "#credit-lineitem-listing", replace: false, async: false, timeout: 6000});
        receivePayment.setAction();
        receivePayment.calcAmountNeeded();
        receivePayment.setAvailableCredits();
        receivePayment.validateAmount();
        $('#modal-spinner').hide();
        return false;
    });

    $(document).off('pjax:success', '#lesson-line-item-listing').on('pjax:success', '#lesson-line-item-listing', function () {
        receivePayment.setAction();
        receivePayment.calcAmountNeeded();
        receivePayment.validateAmount();
        return false;
    });

    $(document).on('modal-success', function(event, params) {
        $('#success-notification').html(params.message).fadeIn().delay(5000).fadeOut();
        if ($('#invoice-payment-listing').length) {
            $.pjax.reload({container: "#invoice-payment-listing", replace: false, async: false, timeout: 6000});
        }
	if ($('#customer-view').length) {
	    $.pjax.reload({container:"#customer-view",replace:false, async: false, timeout: 6000});
    }
        return false;
    });

    $(document).off('click', '.modal-back').on('click', '.modal-back', function() {
        $('#modal-spinner').show();
        var lessonIds = $('#lesson-line-item-grid').yiiGridView('getSelectedRows');
        var invoiceIds = $('#invoice-line-item-grid').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds) && $.isEmptyObject(invoiceIds)) {
            $('#modal-spinner').hide();
            $('#index-error-notification').html("Choose any lessons or invoices to create PFI").fadeIn().delay(5000).fadeOut();
        } else {
            $('.modal-back').attr('disabled', true);
            $('.modal-save-replaced').attr('disabled', true);
            var params = $.param({ 'PaymentFormLessonSearch[lessonIds]': lessonIds, 'ProformaInvoice[invoiceIds]': invoiceIds });
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
