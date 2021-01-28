<?php

use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="row user-create-form">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['lesson/edit-cost', 'id' => $model->id])
    ]); ?>

    <div class="row">
        <div class="col-xs-6 pull-left">
            <label class="dollar-symbol">Teacher Cost</label>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'teacherRate')->textInput([
                'value' => round($model->teacherRate,2),
                'class' => 'text-right form-control'
            ])->label(false); ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Cost</h4>');
    });
</script>
