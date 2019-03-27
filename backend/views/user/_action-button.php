<?php
use yii\helpers\Html;
use common\models\User;
use yii\helpers\Url;

$loggedUser = User::findOne(Yii::$app->user->id);
$user = User::findOne($model->id);
?>
    <div class="dropdown">
        <i class="fa fa-gear dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
        <?php if ($user->isCustomer()) : ?>
            <li><a id="receive-payments" href="#">Receive Payment</a></li>
            <li><a id="print-customer-statement" href="#">Print Statement</a></li>
            <li><a id="mail-customer-statement" href="#">Email Statement</a></li>
        <?php endif ; ?>
        <?php if (($loggedUser->isAdmin()) || ($loggedUser->isOwner() && $user->isManagableByOwner()) || ($loggedUser->isStaff() && $user->isManagableByStaff())) : ?>
            <li><a class="user-delete-button" href="<?= Url::to(['delete', 'id' => $model->id]);?>">Delete</a></li>
        <?php endif ; ?>
        </ul>
    </div>
    
<script>
	$(document).off('click', '#receive-payments').on('click', '#receive-payments', function () {
        $.ajax({
            url    : '<?= Url::to(['payment/receive', 'PaymentFormLessonSearch[userId]' => $model->id, 
                'PaymentFormGroupLessonSearch[userId]' => $model->id]); ?>',
            type   : 'get',
            dataType: 'json',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                }
            }
        });
        return false;
    });

    $(document).off('click', '#mail-customer-statement').on('click', '#mail-customer-statement', function() {
        var userId = '<?= $model->id ?>';
        var params = $.param({ 'id' : userId});     
            $.ajax({
                url    : '<?= Url::to(['email/customer-statement']) ?>?' + params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        $('#modal-content').html(response.data);    
                        $('#popup-modal').modal('show');                   
                    }
                }
            });
        return false;
    });

    $(document).off('click', '#print-customer-statement').on('click', '#print-customer-statement', function() {
        var userId = '<?= $model->id ?>';
        var params = $.param({ 'id' : userId});
        var url = '<?= Url::to(['print/customer-statement']) ?>?' + params;
        window.open(url, '_blank');
        return false;
    });
</script>