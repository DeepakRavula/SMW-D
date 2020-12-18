<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Classroom;
use yii\helpers\ArrayHelper;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
    .col-xs-3 {
        width: 23%;
    }
</style>
<div id="edit-classroom" class="edit-classroom">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['private-lesson/edit-online-type', 'PrivateLesson[lessonIds]' => $model->lessonIds]),
    ]); ?>
       <div class="row">
        <div class="col-md-6">
            <?php
            $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
            ?>

           <div class="radio">
            <label><input type="radio" name="online" id="make_online" value="1" checked>Make Online</label>
           </div>
           <div class="radio">
            <label><input type="radio" name="online" id="make_inclass" value="0">Make In Class </label>
           </div>
    
        </div>
        </div>
   
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width': '600px'});
        $('#popup-modal .modal-body').css({'height': '200px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit online type</h4>');   
    });
</script>
