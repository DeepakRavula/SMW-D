<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify p-10">

<?php $form = ActiveForm::begin([
    'id' => 'modal-form',
    'action' => Url::to(['invoice-line-item/update', 'id' => $model->id])
]); ?>
    <div id="item-edit-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <dl class="dl-horizontal item-main-view">
        <div class="row item-code">
            <div class="col-md-12">
                <dt>Code</dt>
                <dd><?= $model->code ?></dd>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <dt>Description</dt>
                <dd><?= $form->field($model, 'description')->textarea()->label(false);?></dd>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7 text-right">
                <dt>Price</dt>
                <dd><?= $form->field($model, 'amount')->textInput(['class' => 'text-right form-control', 
                    'id' => 'amount-line', 'value' => number_format(round($model->amount,0),2,'.','')])->label(false);?></dd>
            </div>
            <?php if (!$model->isOpeningBalance() && !$model->isLessonCredit()) : ?>
                <?php if (Yii::$app->user->can('administrator') || Yii::$app->user->can('owner')) :?>
                    <div class="col-md-5 text-right">
                        <dl class="item-view">
                            <dt>Cost</label></dt>
                            <dd><?= $form->field($model, 'cost')->textInput(['class' => 'text-right form-control'])->label(false);?></dd>
                        </dl>
                    </div>
                <?php endif;?>
                <?php endif;?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <dt>Quantity</dt>
                <dd><?= $form->field($model, 'unit')->textInput(['class' => 'text-right form-control', 'id' => 'unit-line'])->label(false);?></dd>
            </div>
        </div>
        <?php if (!$model->isOpeningBalance() && !$model->isLessonCredit()) : ?>
            <div class="row">
                <div class="col-md-12">
                    <dt></dt>
                    <dd><?= $form->field($model, 'royaltyFree')->checkbox();?></dd>
                </div>
            </div>
        <?php endif;?>
    </dl>
    <?php ActiveForm::end(); ?>
</div>
<script>
 $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Line Item</h4>');
        $('.modal-cancel').show();
        $('.modal-save').hide();
        $('.modal-save-all').addClass('edit-line-item-save');
        $('.modal-save-all').show();
        $('.modal-save-all').text('save');
        $('#modal-back').hide();
        $('#popup-modal .modal-dialog').css({'width': '600px'});
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    });
    $(document).off('click', '.edit-line-item-save').on('click', '.edit-line-item-save', function () {
        var royaltyFreeisChecked = $('#invoicelineitem-royaltyfree').is(':checked');
        if(royaltyFreeisChecked) {
        bootbox.confirm({
            message: "Are you sure you want to save this item as royalty free?",
                callback: function(result){
                    if(result) {
                        $('.bootbox').modal('hide');
                        $.ajax({
                            url: $('#modal-form').attr('action'),
                            type: 'post',
                            dataType: "json",
                            data: $('#modal-form').serialize(),
                            success: function (response)
                            {
                                if (response.status) {
                                    $.pjax.reload({container: '#invoice-view-lineitem-listing', timeout: 6000, async:false});
                                    $.pjax.reload({container: '#invoice-header-summary', timeout: 6000, async:false});
                                    $.pjax.reload({container: '#invoice-bottom-summary', timeout: 6000, async:false});
                                    $('#popup-modal').modal('hide');
                                } else {
                                    $('#invoice-error-notification').html(response.errors).fadeIn().delay(5000).fadeOut();
                                }
                            }
                       });
                       return false;
                    } else {
                        $('.bootbox').modal('hide');
                        return false;
                    }
                }
        });
        }
        else {
            $.ajax({
                            url: $('#modal-form').attr('action'),
                            type: 'post',
                            dataType: "json",
                            data: $('#modal-form').serialize(),
                            success: function (response)
                            {
                                if (response.status) {
                                    $.pjax.reload({container: '#invoice-view-lineitem-listing', timeout: 6000, async:false});
                                    $.pjax.reload({container: '#invoice-header-summary', timeout: 6000, async:false});
                                    $.pjax.reload({container: '#invoice-bottom-summary', timeout: 6000, async:false});
                                    $('#popup-modal').modal('hide');
                                } else {
                                    $('#invoice-error-notification').html(response.errors).fadeIn().delay(5000).fadeOut();
                                }
                            }
                       });

        }
        return false;
    });  
</script>