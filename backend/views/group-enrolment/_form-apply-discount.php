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
<div id="apply-discount-modal" class="apply-discount-form">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['group-enrolment/edit-discount', 'enrolmentId' => $model->id]),
    ]); ?>
    <div class="row">
        <div class="col-xs-5 pull-left">
            <label class="dollar-symbol">Discount</label>
        </div>
        <div class="col-xs-4 btn-group on-off">
            <button class="btn btn-default" data-size="mini" id="off">$</button>
            <button class="btn btn-default" data-size="mini" id="on">%</button>
        </div>
        <div class="col-xs-3">
            <div class="col-xs-1 discount-edit-label">
                <label class="off discount-dollar-symbol on-off-symbol">$</label>
            </div>
            <?= $form->field($discount, 'discount')->textInput([
                    'value' => Yii::$app->formatter->asDecimal($discount->discount, 2),
                    'class' => 'text-right form-control'])->label(false); ?>
        </div>
        <label class="on percent dollar-symbol on-off-symbol">%</label>
    </div>
    <?= $form->field($discount, 'discountType')->hiddenInput()->label(false); ?>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).off('click', '#on').on('click', '#on', function () {
        $('#on').addClass('btn-info');
        $('#off').removeClass('btn-info');
        $('.on').show();
        $('.off').hide();
        $('#enrolmentdiscount-discounttype').val(0);
        return false;
    });

    $(document).off('click', '#off').on('click', '#off', function () {
        $('#off').addClass('btn-info');
        $('#on').removeClass('btn-info');
        $('.on').hide();
        $('.off').show();
        $('#enrolmentdiscount-discounttype').val(1);
        return false;
    });

    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Discount</h4>');
        var button = '<?= $discount->discountType;?>';
        if (button == '0') {
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