<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
@media (min-width: 768px) {
  .adjust-tax dt {
    float: left;
    width: 200px;
    overflow: hidden;
    clear: left;
    text-align: left;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .adjust-tax dd {
      text-align: right;
      width: 100px;
    margin-left: 270px;
}
</style>
<div id="apply-discount-modal" class="apply-discount-form">
    <?php $form = ActiveForm::begin([
        'id' => 'adjust-tax-form',
        'action' => Url::to(['invoice/adjust-tax', 'id' => $model->id]),
    ]); ?>
    
    <div id="tax-adj-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <dl class="dl-horizontal adjust-tax">
        <dt>Tax Calculated</dt>
        <dd><?= Yii::$app->formatter->asDecimal($model->tax, 2); ?></dd>
        <dt>Adjustment</dt>
        <dd><input type="text" id="invoice-tax-adj" class="form-control text-right" name="Invoice[taxAdjusted]" value="0"></dd>
        <dt>Adjusted Tax</dt>
        <dd class="final-tax"><?= Yii::$app->formatter->asDecimal($model->tax, 2); ?></dd>
    </dl>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::a('Cancel', '', ['class' => 'btn btn-default tax-adj-cancel']);?>    
               <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
$(document).off('beforeSubmit', '#adjust-tax-form').on('beforeSubmit', '#adjust-tax-form', function () {
    $('#tax-adj-spinner').show();
    $.ajax({
        url    : $(this).attr('action'),
        type   : 'post',
        dataType: "json",
        data   : $(this).serialize(),
        success: function(response)
        {
            if(response.status)
            {
                $.pjax.reload({container: "#invoice-view", replace: false, async: false, timeout: 6000});
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

$(document).on('click', '#tax-adj-cancel', function () {
    $('#adjust-tax-modal').modal('hide');
});

$(document).on('change', '#invoice-tax-adj', function () {
    var taxCalculated = parseFloat('<?= $model->tax;?>');
    var tax = parseFloat($(this).val());
    $('.final-tax').text((taxCalculated + tax).toFixed(2));
});
</script>
