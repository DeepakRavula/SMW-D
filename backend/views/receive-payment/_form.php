<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

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
?>
<div id="index-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="receive-payment-form">

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['payment/receive']),
        // 'enableAjaxValidation' => true,
        // 'validationUrl' => Url::to(['payment/validate-receive'])
    ]); ?>

    <div class="row">
        <div class="col-xs-3">
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
            <?= $form->field($model, 'amount')->textInput(['class' => 'text-right form-control'])->label('Amount Received'); ?>
        </div>
    </div>

    <?= $form->field($model, 'amountNeeded')->hiddenInput(['id' => 'amount-needed-value'])->label(false); ?>
    <?= $form->field($model, 'selectedCreditValue')->hiddenInput(['id' => 'selected-credit-value'])->label(false); ?>

    <?php ActiveForm::end(); ?>

    <?= Html::label('Lessons', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_lesson-line-item', [
        'model' => $model,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
        'searchModel' => $searchModel
    ]);
    ?>
    
    <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_invoice-line-item', [
        'model' => $model,
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        'searchModel' => $searchModel
    ]);
    ?>
    
    <?= Html::label('Credits', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_credits-available', [
        'model' => $model,
        'creditDataProvider' => $creditDataProvider,
    ]);
    ?>
    <h4 class="pull-right amount-needed">Available Credits $<span class="credit-available">0.00</span></h4>
</div>

<script>
    var receivePayment = {
        setAction: function() {
            var lessonId = <?= $searchModel->lessonId ?>;
            var lessonIds = $('#lesson-line-item-grid').yiiGridView('getSelectedRows');
            var invoiceIds = $('#invoice-line-item-grid').yiiGridView('getSelectedRows');
            var creditIds = $('#credit-line-item-grid').yiiGridView('getSelectedRows');
            var canUseCustomerCredits = 0;
            var canUseInvoiceCredits = 0;
            $('.credit-items-value').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")){
                    var creditType = $(this).find('.credit-type').text();
                    if (creditType == 'Invoice Credit') {
                        canUseInvoiceCredits = 1;
                    } else if (creditType == 'Customer Credit') {
                        canUseCustomerCredits = 1;
                    }
                }
            });
            var params = $.param({ 'PaymentFormLessonSearch[lessonId]' : lessonId, 'PaymentFormLessonSearch[lessonIds]': lessonIds, 
                'PaymentForm[invoiceIds]': invoiceIds, 'PaymentForm[canUseCustomerCredits]': canUseCustomerCredits, 
                'PaymentForm[canUseInvoiceCredits]': canUseInvoiceCredits, 'PaymentForm[creditIds]': creditIds });
            var url = '<?= Url::to(['payment/receive']) ?>?' + params;
            $('#modal-form').attr('action', url);
            return false;
        },
        calcAmountNeeded : function() {
            var amount = parseFloat('0.00');
            $('.line-items-value').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")){
                    var balance = $(this).find('.invoice-value').text();
                    balance = balance.replace('$', '');
                    amount = parseFloat(amount) + parseFloat(balance);
                }
            });
            var creditAmount = parseFloat('0.00');
            $('.credit-items-value').each(function() {
                if ($(this).find('.check-checkbox').is(":checked")){
                    var balance = $(this).find('.credit-value').text();
                    balance = balance.replace('$', '');
                    creditAmount = parseFloat(creditAmount) + parseFloat(balance);
                }
            });
            $('#selected-credit-value').val((creditAmount).toFixed(2));
            $('#amount-needed-value').val((amount).toFixed(2));
            $('.amount-needed-value').text((amount).toFixed(2));
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
        }
    };

    $(document).ready(function () {
        var header = '<div class="row"> <div class="col-md-6"> <h4 class="m-0">Receive Payment</h4> </div> <div class="col-md-6"> <h4 class="amount-needed pull-right">Amount Needed $<span class="amount-needed-value">0.00</span></h4> </div> </div>'; 
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#popup-modal').find('.modal-header').html(header);
        $('.modal-save').text('Pay');
        $('.select-on-check-all').prop('checked', true);
        receivePayment.setAction();
        receivePayment.calcAmountNeeded();
        receivePayment.setAvailableCredits();
    });

    $(document).off('change', '#credit-line-item-grid, #invoice-line-item-grid, #lesson-line-item-grid .select-on-check-all, input[name="selection[]"]').on('change', '#credit-line-item-grid, #invoice-line-item-grid, #lesson-line-item-grid .select-on-check-all, input[name="selection[]"]', function () {
        receivePayment.setAction();
        receivePayment.calcAmountNeeded();
        return false;
    });

    $(document).off('pjax:success', '#lesson-line-item-listing').on('pjax:success', '#lesson-line-item-listing', function () {
        receivePayment.setAction();
        receivePayment.calcAmountNeeded();
        return false;
    });

    $(document).on('modal-success', function(event, params) {
        $('#success-notification').html(params.message).fadeIn().delay(5000).fadeOut();
        if ($('#invoice-payment-listing').length) {
            $.pjax.reload({container: "#invoice-payment-listing", replace: false, async: false, timeout: 6000});
        }
        return false;
    });

    $(document).off('click', '.modal-save-all').on('click', '.modal-save-all', function() {
        $('#modal-spinner').show();
        var lessonIds = $('#lesson-line-item-grid').yiiGridView('getSelectedRows');
        var invoiceIds = $('#invoice-line-item-grid').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds) && $.isEmptyObject(invoiceIds)) {
            $('#modal-spinner').hide();
            $('#index-error-notification').html("Choose any lessons to create PFI").fadeIn().delay(5000).fadeOut();
        } else {
            $('.modal-save-all').attr('disabled', true);
            $('.modal-save-replaced').attr('disabled', true);
            var params = $.param({ 'ProformaInvoice[lessonIds]': lessonIds, 'ProformaInvoice[invoiceIds]': invoiceIds });
            $.ajax({
                url    : '<?= Url::to(['proforma-invoice/create']) ?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    alert(response.status);
                    if (response.status) {
                        window.location.href = response.url;
                    }
                }
            });
        }
        return false;
    });
</script>
