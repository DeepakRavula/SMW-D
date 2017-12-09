<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\touchspin\TouchSpin;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
    .col-xs-6 {
        width: 45%;
    }
    .btn-group {
        position: relative;
        display: inline-block;
        vertical-align: middle;
        left: 30px;
    }
</style>
<div id="apply-discount-modal" class="apply-discount-form">
    <?php $form = ActiveForm::begin([
        'id' => 'apply-discount-form',
        'action' => Url::to(['invoice-line-item/apply-discount', 'InvoiceLineItem[ids]' => $lineItemIds]),
    ]); ?>
    <div id="discount-spinner" class="spinner on-off-symbol">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    
        <?php if (!$model->isOpeningBalance()) : ?>
        <?php if ($model->isLessonItem()) : ?>
    <div class="row">
        <div class="col-xs-4 pull-left">
            <label class="dolar-symbol">Payment Frequency Discount</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-6">
            <div class="col-xs-1">
            </div>
            <?= $form->field($paymentFrequencyDiscount, 'value')->widget(TouchSpin::classname(), [
                'options' => [
                    'placeholder' => '[multiple]',
                    'class' => 'text-right'
                ],
                'pluginOptions' => [
                    'initval' => Yii::$app->formatter->asDecimal($paymentFrequencyDiscount->value, 2),
                    'decimals' => 2,
                    'step' => 0.1,
                    'buttonup_class' => 'btn btn-default', 
                    'buttondown_class' => 'btn btn-default', 
                    'buttonup_txt' => '<i class="glyphicon glyphicon-chevron-up"></i>', 
                    'buttondown_txt' => '<i class="glyphicon glyphicon-chevron-down"></i>'
                ],
            ])->label(false); ?>
        </div>
        <label class="percen dolar-symbol">%</label>
    </div>
        <?php endif; ?>
    <div class="row">
        <div class="col-xs-4 pull-left">
            <label class="dolar-symbol">Customer Discount</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-6">
            <div class="col-xs-1">
            </div>
            <?= $form->field($customerDiscount, 'value')->widget(TouchSpin::classname(), [
                'options' => [
                    'placeholder' => '[multiple]',
                    'class' => 'text-right'
                ],
                'pluginOptions' => [
                    'initval' => Yii::$app->formatter->asDecimal($customerDiscount->value, 2),
                    'decimals' => 2,
                    'step' => 0.1,
                    'buttonup_class' => 'btn btn-default', 
                    'buttondown_class' => 'btn btn-default', 
                    'buttonup_txt' => '<i class="glyphicon glyphicon-chevron-up"></i>', 
                    'buttondown_txt' => '<i class="glyphicon glyphicon-chevron-down"></i>'
                ],
            ])->label(false); ?>
        </div>
        <label class="percen dolar-symbol">%</label>
    </div>
        <?php if ($model->isLessonItem()) : ?>
    <div class="row">
        <div class="col-xs-4 pull-left">
            <label class="dolar-symbol">Multiple Enrollment Discount</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-6">
            <div class="col-xs-1">
                <label class="dolar-symbol">$</label>
            </div>
            <?= $form->field($multiEnrolmentDiscount, 'value')->widget(TouchSpin::classname(), [
                'options' => [
                    'placeholder' => '[multiple]',
                    'class' => 'text-right'
                ],
                'pluginOptions' => [
                    'initval' => Yii::$app->formatter->asDecimal($multiEnrolmentDiscount->value, 2),
                    'decimals' => 2,
                    'step' => 0.1,
                    'buttonup_class' => 'btn btn-default', 
                    'buttondown_class' => 'btn btn-default', 
                    'buttonup_txt' => '<i class="glyphicon glyphicon-chevron-up"></i>', 
                    'buttondown_txt' => '<i class="glyphicon glyphicon-chevron-down"></i>'
                ],
            ])->label(false); ?>
        </div>
    </div>
        <?php endif; ?>
    <div class="row">
        <div class="col-xs-4 pull-left">
            <label class="dolar-symbol">Line Item Discount</label>
        </div>
        <div class="col-xs-2 btn-group">
            <button class="btn btn-default" data-size="mini" id="off">$</button>
            <button class="btn btn-default" data-size="mini" id="on">%</button>
        </div>
        <div class="col-xs-6">
            <div class="col-xs-1 discount-edit-label">
                <label class="off dolar-symbol on-off-symbol">$</label>
            </div>
            <?= $form->field($lineItemDiscount, 'value')->widget(TouchSpin::classname(), [
                'options' => [
                    'placeholder' => '[multiple]',
                    'class' => 'text-right'
                ],
                'pluginOptions' => [
                    'initval' => Yii::$app->formatter->asDecimal($lineItemDiscount->value, 2),
                    'decimals' => 2,
                    'step' => 0.1,
                    'buttonup_class' => 'btn btn-default', 
                    'buttondown_class' => 'btn btn-default', 
                    'buttonup_txt' => '<i class="glyphicon glyphicon-chevron-up"></i>', 
                    'buttondown_txt' => '<i class="glyphicon glyphicon-chevron-down"></i>'
                ],
            ])->label(false); ?>
        </div>
        <label class="on percen dolar-symbol on-off-symbol">%</label>
        <?php endif; ?> 
    </div>
    <?= $form->field($lineItemDiscount, 'valueType')->hiddenInput()->label(false); ?>
    
    <?php ActiveForm::end(); ?>
</div>
<?php $message = 'Warning: You have entered a non-approved Arcadia discount. All non-approved discounts must be submitted in writing and approved by Head Office prior to entering a discount, otherwise you are in breach of your agreement.'; ?>
<script>
$(document).off('click', '.apply-discount-form-save').on('click', '.apply-discount-form-save', function () {
    $('#discount-spinner').show();
    var message = '<?= $message;?>';
    $.ajax({
        url    : $('#apply-discount-form').attr('action'),
        type   : 'post',
        dataType: "json",
        data   : $('#apply-discount-form').serialize(),
        success: function(response)
        {
            if(response.status)
            {
                $.pjax.reload({container: "#invoice-view-lineitem-listing", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-header-summary", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                $('#invoice-discount-warning').html(message).fadeIn().delay(8000).fadeOut();
                $('#apply-discount-modal').modal('hide');
                $('#discount-spinner').hide();
            } else {
                $('#discount-spinner').hide();
                $(this).yiiActiveForm('updateMessages', response.errors , true);
            }
        }
    });
    return false;
});

$(document).off('click', '#on').on('click', '#on', function () {
    $('#on').addClass('btn-info');
    $('#off').removeClass('btn-info');
    $('.on').show();
    $('.off').hide();
    $('#lineitemdiscount-valuetype').val(1);
    return false;
});

$(document).off('click', '#off').on('click', '#off', function () {
    $('#off').addClass('btn-info');
    $('#on').removeClass('btn-info');
    $('.on').hide();
    $('.off').show();
    $('#lineitemdiscount-valuetype').val(0);
    return false;
});

$(document).ready(function() {
    var button = '<?= $lineItemDiscount->valueType;?>';
    if (button == '1') {
        $('#on').addClass('btn-info');
        $('#off').removeClass('btn-info');
        $('.on').show();
        $('.off').hide();
    } else {
        $('#off').addClass('btn-info');
        $('#on').removeClass('btn-info');
        $('.on').hide();
        $('.off').show();
    }
});
</script>
