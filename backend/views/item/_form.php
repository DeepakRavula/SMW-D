<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\switchinput\SwitchInput;
use yii\bootstrap\ActiveForm;
use common\models\Item;
use common\models\ItemCategory;
use common\models\TaxStatus;
use yii\helpers\Url;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Item */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="item-form">

    <?php   $url = Url::to(['item/update', 'id' => $model->id]);
            if ($model->isNewRecord) {
                $url = Url::to(['item/create']);
            }
        $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
    ]); ?>
<div class="row">
    <div class="col-xs-6">
        <?php echo $form->field($model, 'itemCategoryId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(ItemCategory::find()
                    ->notDeleted()
                    ->active()
                   ->orderBy(['name' => SORT_ASC])
                    ->all(), 'id', 'name'),
                'options' => ['placeholder' => 'Select Category'],
            ]);
        ?>
    </div>
    <div class="col-xs-6">
        <?php echo $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-12">
        <?php echo $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-6">
        <?php echo $form->field($model, 'price')->textInput() ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'royaltyFree')->widget(
            SwitchInput::classname(),
                    [
                    'name' => 'royaltyFree',
                    'pluginOptions' => [
                        'handleWidth' => 30,
                        'onText' => 'Yes',
                        'offText' => 'No',
                    ],
                ]
        );?>
    </div>
    <div class="col-xs-6">
        <?php echo $form->field($model, 'taxStatusId')->dropDownList(ArrayHelper::map(TaxStatus::find()->all(), 'id', 'name'), ['prompt' => 'Select Tax']) ?>
    </div>
    <div class="col-xs-6">
        <?php echo $form->field($model, 'status')->dropDownList(Item::itemStatuses()) ?>
    </div>
</div>
    <?php ActiveForm::end(); ?>

</div>
<script>
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Item</h4>');
        $('.modal-cancel').show();
         $('.modal-save').hide();
        $('.modal-save-all').addClass('item-create');
        $('.modal-save-all').show();
        $('.modal-save-all').text('save');
        $('#modal-back').hide();
        $('#popup-modal .modal-dialog').css({'width': '600px'});
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    });
    $(document).off('click', '.item-create').on('click', '.item-create', function () {
        var royaltyFreeisChecked = $('#item-royaltyfree').is(':checked');
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
                                    $.pjax.reload({container: '#item-listing', timeout: 6000});
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
                                    $.pjax.reload({container: '#item-listing', timeout: 6000});
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