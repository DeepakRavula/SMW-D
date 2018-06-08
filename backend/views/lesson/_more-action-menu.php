<?php

use yii\helpers\Url;
use common\Models\User;
?>

<div class="m-b-10 pull-right">
    <div class="btn-group">
        <button class="btn dropdown-toggle" data-toggle="dropdown">More Action&nbsp;&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="receive-payment" href="#">Receive Payment</a></li>
            <li><a id="lesson-mail-button" href="#">Mail</a></li>
            <?php if ($model->isPrivate()) : ?>
                <?php if ($model->canExplode()) : ?>
                    <li><a id="split-lesson" href="#">Explode</a></li>
                <?php endif; ?>
                <?php $loggedUser = User::findOne(Yii::$app->user->id); ?>
                <?php if ($model->canMerge() && $loggedUser->canMerge) : ?>
                    <li><a id="merge-lesson" href="#">Merge</a></li>
                <?php endif; ?>
                <?php if ($model->isDeletable()) : ?>
                    <li><a id="lesson-delete" href="#">Delete</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script>
    $(document).on('click', '#split-lesson', function () {
        $.ajax({
            url    : '<?= Url::to(['private-lesson/split', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: 'json'
        });
        return false;
    });

    $(document).on('click', '#receive-payment', function () {
        $.ajax({
            url    : '<?= Url::to(['payment/receive', 'PaymentForm[lessonId]' => $model->id]); ?>',
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
