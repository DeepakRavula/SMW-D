<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div id="apply-discount-modal" class="apply-discount-form">
    <?php $form = ActiveForm::begin([
        'id' => 'apply-discount-form',
        'action' => Url::to(['invoice-line-item/apply-discount', 'InvoiceLineItem[ids]' => $lineItemIds]),
    ]); ?>
    <div class="row">
        <?php if (!$model->isOpeningBalance()) : ?>
        <?php if ($model->isLessonItem()) : ?>
        <div class="col-xs-6 pull-left">
            <label>Payment Frequency Discount</label>
        </div>
        <div class="col-xs-4">
            <?= $form->field($paymentFrequencyDiscount, 'value', ['inputTemplate' => '<div class="input-group">'
		. '{input}<span class="input-group-addon" style="background-color:lightgrey;">%</span></div>'])
                ->textInput(['placeholder' => 'nochange', 'class' => 'form-control text-right'])->label(false); ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($paymentFrequencyDiscount, 'clearValue')->checkbox(['class' => 'clear-value'])->label('clear'); ?>
        </div>
        <?php endif; ?>
        <div class="col-xs-6 pull-left">
            <label>Customer Discount</label>
        </div>
        <div class="col-xs-4">
            <?= $form->field($customerDiscount, 'value', ['inputTemplate' => '<div class="input-group">'
		. '{input}<span class="input-group-addon" style="background-color: lightgrey;">%</span></div>'])
                ->textInput(['placeholder' => 'nochange', 'class' => 'form-control text-right'])->label(false); ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($customerDiscount, 'clearValue')->checkbox(['class' => 'clear-value'])->label('clear'); ?>
        </div>
        <?php if ($model->isLessonItem()) : ?>
        <div class="col-xs-6 pull-left">
            <label>Multiple Enrollment Discount</label>
        </div>
        <div class="col-xs-4">
            <?= $form->field($multiEnrolmentDiscount, 'value', ['inputTemplate' => '<div class="input-group">'
		. '<span class="input-group-addon" style="background-color: lightgrey;">$</span>{input}</div>'])
                ->textInput(['placeholder' => 'nochange', 'class' => 'form-control text-right'])->label(false); ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($multiEnrolmentDiscount, 'clearValue')->checkbox(['class' => 'clear-value'])->label('clear'); ?>
        </div>
        <?php endif; ?>
        <div class="col-xs-5 pull-left">
            <label>Line Item Discount</label>
        </div>
        <div class="col-xs-2">
            <?= $form->field($lineItemDiscount, 'valueType')->widget(SwitchInput::classname(),
		[
                'name' => 'valueType',
                'pluginOptions' => [
                    'handleWidth' => 20,
                    'onText' => '%',
                    'offText' => '$',
                ],
            ])->label(false); ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($lineItemDiscount, 'value')->textInput([
                'placeholder' => 'nochange', 'class' => 'form-control text-right'])->label(false); ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($lineItemDiscount, 'clearValue')->checkbox(['class' => 'clear-value'])->label('clear'); ?>
        </div>
        <?php endif; ?> 
    </div>

    <div class="row">
    <div class="col-md-12">
        <div class="pull-right">
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default invoice-apply-discount-cancel']);?>    
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
    </div>
    </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php $message = 'Warning: You have entered a non-approved Arcadia discount. All non-approved discounts must be submitted in writing and approved by Head Office prior to entering a discount, otherwise you are in breach of your agreement.'; ?>
<script>
$(document).off('beforeSubmit', '#apply-discount-form').on('beforeSubmit', '#apply-discount-form', function () {
    var message = '<?= $message;?>';
    $.ajax({
        url    : $(this).attr('action'),
        type   : 'post',
        dataType: "json",
        data   : $(this).serialize(),
        success: function(response)
        {
            if(response.status)
            {
                $.pjax.reload({container: "#invoice-view-lineitem-listing", replace: false, async: false, timeout: 6000});
                $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                $('#invoice-discount-warning').html(message).fadeIn().delay(8000).fadeOut();
                $('#apply-discount-modal').modal('hide');
            } else {
                $(this).yiiActiveForm('updateMessages', response.errors , true);
            }
        }
    });
    return false;
});

$(document).on('change', '.clear-value', function () {
    var id = $(this).attr('id');
    var value = id.split("-");
    if ($(this).is(':checked')) {
        $('#' + value[0] + '-value').val('');
    }
});
</script>
