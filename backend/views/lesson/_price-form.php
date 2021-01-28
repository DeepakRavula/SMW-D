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
        'action' => Url::to(['lesson/edit-price', 'id' => $model->id]),
    ]); ?>
    <div class="row">
        <div class="col-xs-6 pull-left">
            <label class="dollar-symbol">Program Rate Per Hour</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'programRate')->textInput([
                    'value' => number_format(round($model->programRate,0),2,'.',''),
                    'class' => 'text-right form-control'
                ])->label(false); 
            ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Price</h4>');
    });
</script>
