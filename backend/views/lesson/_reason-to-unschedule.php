<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\ReasonsToUnschedule;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
     <?php $url = Url::to(['unscheduled-lesson/bulk-unschedule', 'UnscheduleLesson[lessonIds]' => $unscheduleLessonModel->lessonIds]);
            if (!$unscheduleLessonModel->isBulk) {
                $url = Url::to(['lesson/unschedule', 'id' => $unscheduleLessonModel->lessonIds]);
            }
            ?>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,    ]); ?>
     <div id = "reason-to-unschedule">
            <?php 
            $reasonsToUnscheduleQuery = ReasonsToUnschedule::find()
            ->notDeleted()
            ->all();
            $reasonsToUnschedule = ArrayHelper::map($reasonsToUnscheduleQuery, 'id', 'reason');
                    echo  $form->field($unscheduleLessonModel, 'reasonToUnschedule')->radioList($reasonsToUnschedule)->label('Reason To Unschedule ?');
                    echo  $form->field($unscheduleLessonModel, 'reason')->textInput()->label(false);
            ?>
        </div>
    <?php ActiveForm::end(); ?>

<script>
    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Reason To Unschedule</h4>');
        $("#unschedulelesson-reason").hide();
    });
    
    $(document).off('click', 'input:radio[name="UnscheduleLesson[reasonToUnschedule]"]').on('click', 'input:radio[name="UnscheduleLesson[reasonToUnschedule]"]', function () {
        var reasonToUnschedule = $('input:radio[name="UnscheduleLesson[reasonToUnschedule]"]:checked').val();
        var content = $('input:radio[name="UnscheduleLesson[reasonToUnschedule]"]:checked').parent('label').text();
        $("#unschedulelesson-reason").val(content);
        if (reasonToUnschedule == '4') {
            $("#unschedulelesson-reason").val('');
            $("#unschedulelesson-reason").show();
        } else {
            $("#unschedulelesson-reason").hide();
        }
    });

</script>
