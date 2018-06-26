<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\time\TimePicker;

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
<div id="edit-duration" class="edit-duration">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['private-lesson/edit-duration', 'PrivateLesson[ids]' => $lessonIds]),
    ]); ?>
       <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'duration')->widget(
            TimePicker::classname(),
                [
                'options' => ['id' => 'lesson-duration', 'placeholder' => $placeholder,],
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => false,
                   
                ],
            ]
        );
            ?>
        </div>
        </div>
   
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
        $('#popup-modal .modal-body').css({'height': '200px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Duration</h4>');
       
    });
</script>
