<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\NotificationEmailType;
?>
    <?php 
     $url = Url::to(['email/notify-email' ,'EmailTypes[reason]' => $emailTypes->emailNotifyType]);
    ?>
<?php $form = ActiveForm::begin([

        'id' => 'modal-form',
        'action' => $url,    ]); ?>
     <div id = "email-notify-reasons">
            <?php 
            $emailReasonsQuery = NotificationEmailType::find()
            ->notDeleted()
            ->all();
            
            $emailNotifyTypes = ArrayHelper::map($emailReasonsQuery, 'id', 'emailNotifyType');
            echo $form->field($emailTypes, 'notificationEmailType')->checkboxList(NotificationEmailType::emailNotifyList());
            ?>
        </div>
    <?php ActiveForm::end(); ?>


<script>
     $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Notify Via Email Reasons</h4>');
        $("#email-notify-reason").hide();
        $('#modal-save').text('Send');
    });
</script>