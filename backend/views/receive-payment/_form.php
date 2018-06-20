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
        <?php Pjax::Begin(['id' => 'payment-amount', 'timeout' => 6000]); ?>
        <div class="col-xs-2">
            <?= $form->field($model, 'amount')->textInput()->label('Amount Received'); ?>
        </div>
        <?php Pjax::end(); ?>
        <div class ="col-xs-4">
        <div class ="m-b-10"></div>
        <div class = "col-xs-6">
        <?= Html::label('Amount Needed', 'xxx');?>
        <?= Html::label('Credit Amount', 'xxx'); ?>
        </div>
        <div class = "col-xs-6">
        <?= Html::label('','amount-need-to-pay',['class' =>'amount-needed']); ?>
        <?= Html::label('','amount-to-credited',['class' =>'amount-credit']); ?>
        </div>
       </div>
    </div>
     
    <div class="pull-right col-md-3">
    <label>Date Range To Filter Lessons</label>
    <?= DateRangePicker::widget([
        'model' => $model,
        'attribute' => 'dateRange',
        'convertFormat' => true,
        'initRangeExpr' => true,
        'options' => [
            'class' => 'form-control',
            'readOnly' => true
        ],
        'pluginOptions' => [
            'autoApply' => true,
            'ranges' => [
                Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')", 'moment()'],
                Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')", 'moment()'],
                Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')", "moment().endOf('month')"],
                Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
            ],
            'locale' => [
                'format' => 'M d,Y'
            ],
            'opens' => 'left'
        ]
    ]); ?>
</div>
    <div class = "row">
	<div class="col-md-12">
    <?= Html::label('Lessons', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_lesson-line-item', [
        'model' => $model,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
        'searchModel'=>$searchModel,
    ]);
    ?>
	</div>
    </div>
    <div class = "row">
	<div class="col-md-12">
    <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_invoice-line-item', [
        'model' => $model,
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        'searchModel'=>$searchModel,
    ]);
    ?>
	</div>
    </div>
    <?php ActiveForm::end(); ?>
    <div class = "row">
	<div class="col-md-12">
    <?= Html::label('Credits', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_credits-available', [
        'model' => $model,
        'creditsAvailable' => $creditsAvailable,
    ]);
    ?>
	</div>
    </div>
</div>

<script>
    var receivePayment = {
        setAction: function() {
            var lessonId = <?= $model->lessonId ?>;
            var lessonIds = $('#lesson-line-item-grid').yiiGridView('getSelectedRows');
            var invoiceIds = $('#invoice-line-item-grid').yiiGridView('getSelectedRows');
            var params = $.param({ 'PaymentForm[lessonId]' : lessonId, 'PaymentForm[lessonIds]': lessonIds, 'PaymentForm[invoiceIds]': invoiceIds });
            var url = '<?= Url::to(['payment/receive']) ?>?' + params;
            $('#modal-form').attr('action', url);
            return false;
        },
        calcAmountNeeded : function() {
            var amount=parseFloat('0.00');
            $('.line-items-value').each(function() {
                if($(this).find('.check-checkbox').is(":checked")){
                amount = parseFloat(amount) + parseFloat($(this).find('.invoice-value').text());  
                }
    });
    alert(amount);
    var creditAmount = 0.00;
                if($('.apply-credit-checkbox').is(":checked")){
                amount = amount - parseFloat($('.credits-available-amount').text());
                alert(amount);
                if(amount < 0){
                    creditAmount = Math.abs(amount);
                    amount ='0.00';
                }
                }
    $('.amount-needed').text(amount);
    $('.amount-credit').text(creditAmount);
    receivePayment.updateCreditAmount();
            return false;
        },
        updateCreditAmount : function() {
            var amountNeeded = $('.amount-needed').val();
            var amountReceived = $('#paymentform-amount').val();
            alert(amountNeeded);
            alert(amountReceived);
            $('.credits-available-amount').text('');
        },
    };

    $(document).ready(function () {    
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Receive Payment</h4>');
        $('.modal-save').text('Pay');
        $('.select-on-check-all').prop('checked', true);
        $('#invoice-line-item-grid .select-on-check-all').prop('disabled', true);
        $('#invoice-line-item-grid input[name="selection[]"]').prop('disabled', true);
        receivePayment.setAction();
        receivePayment.calcAmountNeeded();
    });

    $(document).off('change', '#paymentform-daterange').on('change', '#paymentform-daterange', function () {
        $('#modal-spinner').show();
        var dateRange = $('#paymentform-daterange').val();
	    var lessonId = <?= $model->lessonId ?>;
        var params = $.param({ 'PaymentForm[dateRange]': dateRange, 'PaymentForm[lessonId]' : lessonId });
        var url = '<?= Url::to(['payment/receive']) ?>?' + params;
	    $.pjax.reload({url:url, container: "#lesson-lineitem-listing", replace: false, async: false, timeout: 6000});
        $.pjax.reload({url:url, container: "#payment-amount", replace: false, async: false, timeout: 6000});
        $('.select-on-check-all').prop('checked', true);
        $('#modal-spinner').hide();
        receivePayment.setAction();
        return false;
    });

    $(document).off('change', '#lesson-line-item-grid .select-on-check-all, input[name="selection[]"]').on('change', '#lesson-line-item-grid .select-on-check-all, input[name="selection[]"]', function () {
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
