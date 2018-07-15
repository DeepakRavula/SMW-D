<?php
use yii\helpers\Html;
use common\models\User;
use yii\helpers\Url;

$loggedUser = User::findOne(Yii::$app->user->id);
$user = User::findOne($model->id);
?>
<div class="btn-group-sm">
    <button class="btn dropdown-toggle" data-toggle="dropdown">More Action&nbsp;&nbsp;<span class="caret"></span></button>
    <ul class="dropdown-menu dropdown-menu-right">
    <?php if ($user->isCustomer()) : ?>
	<li><a id="receive-payments" href="#">Receive Payment</a></li>
    <?php endif ; ?>
	<?php if(($loggedUser->isAdmin()) || ($loggedUser->isOwner() && $user->isManagableByOwner()) || ($loggedUser->isStaff() && $user->isManagableByStaff())) { ?>
	<li><a class="user-delete-button" href="<?= Url::to(['delete', 'id' => $model->id]);?>">Delete</a></li>
    </ul>
    <?php } ?>
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
</script>