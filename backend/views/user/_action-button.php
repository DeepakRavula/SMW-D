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
            <li><a id="ar-report-detail" href="#">A/R Report Detail</a></li>
            <li><a id="item-report-detail" href="#">Items Purchased by Category</a></li>
        <?php endif ; ?>
        <?php if (($loggedUser->isAdmin()) || ($loggedUser->isOwner() && $user->isManagableByOwner()) || ($loggedUser->isStaff() && $user->isManagableByStaff())) : ?>
            <li><a id="notify-email-toggle" href="#">Notify Via Email</a></li>
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

    $(document).off('click', '#notify-email-toggle').on('click', '#notify-email-toggle', function() {
       var userId = '<?= $model->id ?>';
        var params = $.param({ 'id' : userId});
        $.ajax({
            url: '<?= Url::to(['email/notify-email-preview']) ?>?' + params,
            type: 'get',
            success: function (response)
            {
                if (response.status)
                {
                    $('#menu-shown').hide();
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                } else {
                    $('#menu-shown').hide();
                    $('#error-notification').html(response.message).fadeIn().delay(3000).fadeOut();
                }
            }
        });
        return false;
    });
    

    $(document).off('click', '#ar-report-detail').on('click', '#ar-report-detail', function() { 
        var url = '<?= Url::to(['account-receivable-report/view' ,'id' => $model->id]); ?>';
        window.open(url,'_blank');
        return false;   
     });

     $(document).off('click', '#item-report-detail').on('click', '#item-report-detail', function() { 
        var url = '<?= Url::to(['customer-items-report/index' ,'id' => $model->id]); ?>';
        window.open(url,'_blank');
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