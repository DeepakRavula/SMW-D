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

<div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['lesson/edit-tax', 'id' => $model->id]),
    ]); ?>
    <div class="row">
        <div class="col-xs-6 pull-left">
            <label class="dollar-symbol">Tax</label>
        </div>
        
        <div class="col-xs-4">
            <?= $form->field($model, 'tax')->textInput()->label(false); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Tax</h4>');
    });
</script>