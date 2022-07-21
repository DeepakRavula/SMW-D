<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\NotificationEmailType;
use common\models\CustomerEmailNotification;
?>
<?php 
 $url = Url::to(['email/notify-email','customerId' => $customerId]);
 ?>
    
<?php $form = ActiveForm::begin([
    'id' => 'modal-form',
    'action' => $url,    ]); ?>
    <div id = "email-notify-reasons">
        <?php 

        $isCheckedLists = CustomerEmailNotification::find()->andwhere(['userId'=> $customerId])->andwhere(['isChecked' => true])->all();
        foreach($isCheckedLists as $isCheckedList) {
            
            $checkedList [] = $isCheckedList->emailNotificationTypeId;
            
        }

        $emailTypes->emailNotifyType = $checkedList;
        ?> 
        <?=
         $form->field ($emailTypes, 'emailNotifyType')->checkboxList(NotificationEmailType::emailNotifyList())->label(false);
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