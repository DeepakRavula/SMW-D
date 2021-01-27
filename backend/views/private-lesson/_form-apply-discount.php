<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
    .col-xs-3 {
        width: 23%;
    }
</style>
<?php   $placeholder = '[multiple]';
        if (count($lessonIds) < 2) {
            $placeholder = '';
        }
?>
<div id="apply-discount-modal" class="apply-discount-form">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['private-lesson/apply-discount', 'LessonDiscount[ids]' => $lessonIds]),
    ]); ?>
    <div class="row">
        <div class="col-xs-7 pull-left">
            <label class="dollar-symbol">Payment Frequency Discount</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-3">
            <div class="col-xs-1">
            </div>
            <?= $form->field($paymentFrequencyDiscount, 'value')->textInput(['placeholder' => $placeholder,
                    'value' => number_Format($paymentFrequencyDiscount->value, 2),
                    'class' => 'text-right form-control'])->label(false); ?>
        </div>
        <label class="percent dollar-symbol">%</label>
    </div>
    <div class="row">
        <div class="col-xs-7 pull-left">
            <label class="dollar-symbol">Customer Discount</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-3">
            <div class="col-xs-1">
            </div>
            <?= $form->field($customerDiscount, 'value')->textInput(['placeholder' => $placeholder,
                    'value' => number_format($customerDiscount->value, 2),
                    'class' => 'text-right form-control'])->label(false); ?>
        </div>
        <label class="percent dollar-symbol">%</label>
    </div>
    <div class="row">
        <div class="col-xs-7 pull-left">
            <label class="dollar-symbol">Multiple Enrollment Discount</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-3">
            <div class="col-xs-1">
                <label class="discount-dollar-symbol">$</label>
            </div>
            <?= $form->field($multiEnrolmentDiscount, 'value')->textInput(['placeholder' => $placeholder,
                    'value' => number_format($multiEnrolmentDiscount->value, 2),
                    'class' => 'text-right form-control'])->label(false); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-7 pull-left">
            <label class="dollar-symbol">Line Item Discount</label>
        </div>
        <div class="col-xs-2 btn-group on-off">
            <button class="btn btn-default" data-size="mini" id="off">$</button>
            <button class="btn btn-default" data-size="mini" id="on">%</button>
        </div>
        <div class="col-xs-3">
            <div class="col-xs-1 discount-edit-label">
                <label class="off discount-dollar-symbol on-off-symbol">$</label>
            </div>
            <?= $form->field($lineItemDiscount, 'value')->textInput(['placeholder' => $placeholder,
                    'value' => number_format($lineItemDiscount->value, 2),
                    'class' => 'text-right form-control'])->label(false); ?>
        </div>
        <label class="on percent dollar-symbol on-off-symbol">%</label>
    </div>
    <?= $form->field($lineItemDiscount, 'valueType')->hiddenInput()->label(false); ?>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).off('click', '#on').on('click', '#on', function () {
        $('#on').addClass('btn-info');
        $('#off').removeClass('btn-info');
        $('.on').show();
        $('.off').hide();
        $('#lineitemlessondiscount-valuetype').val(1);
        return false;
    });

    $(document).off('click', '#off').on('click', '#off', function () {
        $('#off').addClass('btn-info');
        $('#on').removeClass('btn-info');
        $('.on').hide();
        $('.off').show();
        $('#lineitemlessondiscount-valuetype').val(0);
        return false;
    });

    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width': '600px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Discount</h4>');
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
