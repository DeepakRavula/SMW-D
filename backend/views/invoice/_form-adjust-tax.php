<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div id="apply-discount-modal" class="apply-discount-form">
    <?php $form = ActiveForm::begin([
        'id' => 'adjust-tax-form',
        'action' => Url::to(['invoice/adjust-tax', 'id' => $model->id]),
    ]); ?>
    
    <div id="tax-adj-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <div class="row" style="height: 34px;">
        <div class="col-md-6">
            <div class="pull-left" style="padding-top:7px;">
                <label>Tax Calculated</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="text-right" style="padding-right:13px;padding-top:7px;">
                <?= Yii::$app->formatter->asDecimal($model->tax, 2); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label>Adjustment</label><label style="padding-left:180px;padding-top:7px;">+/-&nbsp;&nbsp;&nbsp;$</label>
            <input type="text" id="invoice-tax-adj" style="width:80px;" class="pull-right text-right form-control" name="Invoice[taxAdjusted]" value="0.00">
        </div>
    </div>
    <div class="row" style="height: 34px;">
        <div class="col-md-6">
            <div class="pull-left" style="padding-top:7px;">
                <label>Adjusted Tax</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="final-tax pull-right" style="padding-right:13px;padding-top:7px;">
                <?= Yii::$app->formatter->asDecimal(0, 2); ?>
            </div>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>

<script>
$(document).off('click', '.adjust-tax-form-save').on('click', '.adjust-tax-form-save', function () {
    $('#tax-adj-spinner').show();
    $.ajax({
        url    : '<?= Url::to(['invoice/adjust-tax', 'id' => $model->id]); ?>',
        type   : 'post',
        dataType: "json",
        data   : $('#adjust-tax-form').serialize(),
        success: function(response)
        {
            if(response.status)
            {
                $.pjax.reload({container: "#invoice-view", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-header-summary", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
                $('#adjust-tax-modal').modal('hide');
                $('#tax-adj-spinner').hide();
            } else {
                $('#tax-adj-spinner').hide();
                $(this).yiiActiveForm('updateMessages', response.errors , true);
            }
        }
    });
    return false;
});

$(document).on('click', '.tax-adj-cancel', function () {
    $('#adjust-tax-modal').modal('hide');
        $.pjax.reload({container: "#invoice-header-summary", replace: false, async: false, timeout: 6000});
    $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
    return false;
});

$(document).on('keyup', '#invoice-tax-adj', function () {
    var taxCalculated = parseFloat('<?= $model->tax;?>');
    var tax = parseFloat($(this).val());
    if ($.isNumeric(tax)) {
        $('.final-tax').text((taxCalculated + tax).toFixed(2));
    } else {
        $('.final-tax').text((0).toFixed(2));
    }
});
</script>
