<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\touchspin\TouchSpin;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div id="apply-discount-modal" class="apply-discount-form">
    <?php $form = ActiveForm::begin([
        'id' => 'apply-discount-form',
        'action' => Url::to(['invoice-line-item/apply-discount', 'InvoiceLineItem[ids]' => $lineItemIds]),
    ]); ?>
    <div id="discount-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    
        <?php if (!$model->isOpeningBalance()) : ?>
        <?php if ($model->isLessonItem()) : ?>
    <div class="row">
        <div class="col-xs-4 pull-left">
            <label style="padding-top:7px;">Payment Frequency Discount</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-1">
        </div>
        <div class="col-xs-4">
            <?= $form->field($paymentFrequencyDiscount, 'value')->widget(TouchSpin::classname(), [
                'options' => [
                    'placeholder' => '[multiple]',
                    'class' => 'text-right'
                ],
                'pluginOptions' => [
                    'initval' => Yii::$app->formatter->asDecimal($paymentFrequencyDiscount->value, 2),
                    'decimals' => 2,
                    'step' => 0.1,
                    'buttonup_class' => 'btn btn-primary', 
                    'buttondown_class' => 'btn btn-info', 
                    'buttonup_txt' => '<i class="glyphicon glyphicon-chevron-up"></i>', 
                    'buttondown_txt' => '<i class="glyphicon glyphicon-chevron-down"></i>'
                ],
            ])->label(false); ?>
        </div>
        <label style="padding-top:7px;">%</label>
    </div>
        <?php endif; ?>
    <div class="row">
        <div class="col-xs-4 pull-left">
            <label style="padding-top:7px;">Customer Discount</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-1">
        </div>
        <div class="col-xs-4">
            <?= $form->field($customerDiscount, 'value')->widget(TouchSpin::classname(), [
                'options' => [
                    'placeholder' => '[multiple]',
                    'class' => 'text-right'
                ],
                'pluginOptions' => [
                    'initval' => Yii::$app->formatter->asDecimal($customerDiscount->value, 2),
                    'decimals' => 2,
                    'step' => 0.1,
                    'buttonup_class' => 'btn btn-primary', 
                    'buttondown_class' => 'btn btn-info', 
                    'buttonup_txt' => '<i class="glyphicon glyphicon-chevron-up"></i>', 
                    'buttondown_txt' => '<i class="glyphicon glyphicon-chevron-down"></i>'
                ],
            ])->label(false); ?>
        </div>
        <label style="padding-top:7px;">%</label>
    </div>
        <?php if ($model->isLessonItem()) : ?>
    <div class="row">
        <div class="col-xs-4 pull-left">
            <label style="padding-top:7px;">Multiple Enrollment Discount</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-1" style="padding-left:35px;">
            <label style="padding-top:7px;">$</label>
        </div>
        <div class="col-xs-4">
            <?= $form->field($multiEnrolmentDiscount, 'value')->widget(TouchSpin::classname(), [
                'options' => [
                    'placeholder' => '[multiple]',
                    'class' => 'text-right'
                ],
                'pluginOptions' => [
                    'initval' => Yii::$app->formatter->asDecimal($multiEnrolmentDiscount->value, 2),
                    'decimals' => 2,
                    'step' => 0.1,
                    'buttonup_class' => 'btn btn-primary', 
                    'buttondown_class' => 'btn btn-info', 
                    'buttonup_txt' => '<i class="glyphicon glyphicon-chevron-up"></i>', 
                    'buttondown_txt' => '<i class="glyphicon glyphicon-chevron-down"></i>'
                ],
            ])->label(false); ?>
        </div>
    </div>
        <?php endif; ?>
    <div class="row">
        <div class="col-xs-4 pull-left">
            <label style="padding-top:7px;">Line Item Discount</label>
        </div>
        <div class="col-xs-2 btn-group">
            <button class="btn btn-default" data-size="mini" id="off">$</button>
            <button class="btn btn-default" data-size="mini" id="on">%</button>
        </div>
        <div class="col-xs-1" style="padding-left:35px;">
            <label class="off" style="padding-top:7px; display: none">$</label>
        </div>
        <div class="col-xs-4">
            <?= $form->field($lineItemDiscount, 'value')->widget(TouchSpin::classname(), [
                'options' => [
                    'placeholder' => '[multiple]',
                    'class' => 'text-right'
                ],
                'pluginOptions' => [
                    'initval' => Yii::$app->formatter->asDecimal($lineItemDiscount->value, 2),
                    'decimals' => 2,
                    'step' => 0.1,
                    'buttonup_class' => 'btn btn-primary', 
                    'buttondown_class' => 'btn btn-info', 
                    'buttonup_txt' => '<i class="glyphicon glyphicon-chevron-up"></i>', 
                    'buttondown_txt' => '<i class="glyphicon glyphicon-chevron-down"></i>'
                ],
            ])->label(false); ?>
        </div>
        <label class="on" style="padding-top:7px; display: none">%</label>
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
