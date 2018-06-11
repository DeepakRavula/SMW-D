<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Url;

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
        'id' => 'modal-form'
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
        <div class="col-xs-4">
            <?= $form->field($model, 'payment_method_id')->dropDownList(ArrayHelper::map($paymentMethods, 'id', 'name'))
                ->label('Payment Method'); ?>
        </div>
        <div class="col-xs-2">
        </div>    
        <?php Pjax::Begin(['id' => 'payment-amount', 'timeout' => 6000]); ?>
        <div class="col-xs-3">
            <?= $form->field($model, 'amount')->textInput()->label('Amount Received'); ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
    <div class = "row">
	<div class="col-md-12">
    <?= Html::label('Lessons', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_lesson-line-item', [
        'model' => $model,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider
    ]);
    ?>
	</div>
    </div>
    <div class = "row">
	<div class="col-md-12">
    <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_invoice-line-item', [
        'model' => $model,
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider
    ]);
    ?>
	</div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function () {
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Receive Payment</h4>');
        $('.modal-save').text('Pay');
        $('.modal-save').removeClass('modal-save').addClass('modal-save-replaced');
        $('.modal-save-all').text('Create PFI');
        $('.modal-save-all').show();
        $('.select-on-check-all').prop('checked', true);
        $('#invoice-line-item-grid .select-on-check-all').prop('disabled', true);
        $('#invoice-line-item-grid input[name="selection[]"]').prop('disabled', true);
    });

    $(document).on('change', '#paymentform-daterange', function () {
        var dateRange = $('#paymentform-daterange').val();
        var params = $.param({ 'PaymentForm[dateRange]': dateRange });
        var url = '<?= Url::to(['payment/receive']) ?>?' + params;
        $.pjax.reload({url: url, container: '#lesson-lineitem-listing', timeout: 6000});
        $.pjax.reload({url: url, container: '#payment-amount', timeout: 6000});
    });

    $(document).off('click', '.modal-save-replaced').on('click', '.modal-save-replaced', function() {
        var lessonId = '<?= $model->lessonId ?>';
        var lessonIds = $('#lesson-line-item-grid').yiiGridView('getSelectedRows');
        var invoiceIds = $('#invoice-line-item-grid').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds) && $.isEmptyObject(invoiceIds)) {
            $('#index-error-notification').html("Choose any lessons to create PFI").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ 'PaymentForm[lessonId]': lessonId, 'PaymentForm[lessonIds]': lessonIds, 'PaymentForm[invoiceIds]': invoiceIds });
            $.ajax({
                url    : '<?= Url::to(['payment/receive']) ?>?' +params,
                type   : 'post',
                dataType: "json",
                data: $('#modal-form').serialize(),
                success: function(response)
                {
                    if (response.status) {
                        $('#popup-modal').modal('hide');
                        $('#success-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        }
        return false;
    });

    $(document).on('click', '.modal-save-all', function() {
        var lessonIds = $('#lesson-line-item-grid').yiiGridView('getSelectedRows');
        var invoiceIds = $('#invoice-line-item-grid').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds) && $.isEmptyObject(invoiceIds)) {
            $('#index-error-notification').html("Choose any lessons to create PFI").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ lessonIds: lessonIds, invoiceIds: invoiceIds });
            $.ajax({
                url    : '<?= Url::to(['proforma-invoice/create']) ?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    alert(response.status);
                    if (response.status) {
                        window.location.href = response.url;
                    } else {
                        //$('#index-error-notification').html("Choose lessons with same teacher").fadeIn().delay(5000).fadeOut();
                    }
                }
            });
                }
            return false;
        });
</script>
